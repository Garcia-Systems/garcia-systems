<?php

namespace App\Support\GarciaContent;

use App\Models\{AssessmentQuestion, Capability, CompanyType, ContentInstallationItem, ContentInstallationRun, Department, FrictionPoint, Industry, SolutionPattern, Workflow};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class StructuredContentInstaller
{
    public const DATASETS = ['assessment', 'atlas', 'all'];

    public function install(string $dataset, bool $dryRun = false): array
    {
        $this->assertDataset($dataset);
        $plan = [];
        $datasets = $dataset === 'all' ? ['assessment', 'atlas'] : [$dataset];

        if ($dryRun) {
            foreach ($datasets as $name) {
                $plan = array_merge($plan, $this->plan($name));
            }
            return ['run' => null, 'items' => $plan];
        }

        $run = ContentInstallationRun::create([
            'uuid' => (string) Str::uuid(), 'dataset' => $dataset, 'status' => 'running',
            'started_at' => now(), 'executed_by' => app()->environment().' / artisan',
            'metadata' => ['datasets' => $datasets],
        ]);

        try {
            DB::transaction(function () use ($datasets, $run) {
                foreach ($datasets as $name) {
                    $name === 'assessment' ? $this->installAssessment($run) : $this->installAtlas($run);
                }
                $run->update(['status' => 'completed', 'completed_at' => now()]);
            });
        } catch (\Throwable $exception) {
            $run->items()->create(['item_type' => 'failure', 'stable_key' => $dataset, 'action' => 'failed', 'metadata' => ['message' => $exception->getMessage()]]);
            $run->update(['status' => 'failed', 'completed_at' => now(), 'metadata' => ($run->metadata ?? []) + ['failure' => $exception->getMessage()]]);
            throw $exception;
        }

        return ['run' => $run->fresh('items'), 'items' => $run->items()->get()->all()];
    }

    public function rollback(ContentInstallationRun $run, bool $dryRun = false): array
    {
        if ($run->status === 'rolled_back') {
            throw new RuntimeException("Run {$run->uuid} has already been rolled back.");
        }
        if ($run->status !== 'completed') {
            throw new RuntimeException("Only completed runs can be rolled back; run {$run->uuid} is {$run->status}.");
        }

        $events = [];
        $work = function () use ($run, &$events) {
            foreach ($run->items()->where('action', 'attached')->latest('id')->get() as $item) {
                $events[] = $this->detachPivot($item);
            }
            foreach ($run->items()->where('action', 'created')->latest('id')->get() as $item) {
                $events[] = $this->removeCreatedModel($item);
            }
            $run->update(['status' => 'rolled_back', 'rolled_back_at' => now()]);
        };

        if ($dryRun) {
            foreach ($run->items()->whereIn('action', ['attached', 'created'])->latest('id')->get() as $item) {
                $events[] = ['action' => 'would_'.$item->action, 'stable_key' => $item->stable_key, 'model_type' => $item->model_type, 'model_id' => $item->model_id];
            }
            return $events;
        }

        DB::transaction($work);
        return $events;
    }

    public function activeState(string $dataset): bool
    {
        return match ($dataset) {
            'assessment' => AssessmentQuestion::query()->where('is_active', true)->exists(),
            'atlas' => Workflow::query()->exists(),
            'all' => AssessmentQuestion::query()->where('is_active', true)->exists() && Workflow::query()->exists(),
            default => false,
        };
    }

    private function plan(string $dataset): array
    {
        $items = [];
        if ($dataset === 'assessment') {
            foreach ($this->assessmentQuestions() as $row) {
                $existing = AssessmentQuestion::where('key', $row['key'])->first();
                $items[] = ['action' => $existing ? ($existing->is_active ? 'would_reuse' : 'would_reactivate') : 'would_create', 'stable_key' => $row['key'], 'item_type' => 'assessment_question'];
            }
        } else {
            foreach ($this->atlasRows()['workflows'] as $row) {
                $items[] = ['action' => Workflow::where('slug', $row['slug'])->exists() ? 'would_reuse' : 'would_create', 'stable_key' => $row['slug'], 'item_type' => 'workflow'];
            }
        }
        return $items;
    }

    private function installAssessment(ContentInstallationRun $run): void
    {
        foreach ($this->assessmentQuestions() as $row) {
            $question = AssessmentQuestion::where('key', $row['key'])->first();
            if (! $question) {
                $question = AssessmentQuestion::create($row + ['is_active' => true]);
                $this->record($run, 'assessment_question', $question, $row['key'], 'created');
            } elseif (! $question->is_active && $this->createdByInstaller($question)) {
                $question->forceFill(['is_active' => true])->save();
                $this->record($run, 'assessment_question', $question, $row['key'], 'reactivated');
            } else {
                $this->record($run, 'assessment_question', $question, $row['key'], 'reused');
            }
        }
    }

    private function installAtlas(ContentInstallationRun $run): void
    {
        $data = $this->atlasRows();
        $models = [];
        foreach (['industries'=>Industry::class,'company_types'=>CompanyType::class,'departments'=>Department::class,'capabilities'=>Capability::class,'solution_patterns'=>SolutionPattern::class] as $key => $class) {
            foreach ($data[$key] as $row) $models[$key][$row['slug']] = $this->firstOrRecord($run, $class, $key, $row);
        }
        foreach ($data['workflows'] as $row) {
            $workflow = $this->firstOrRecord($run, Workflow::class, 'workflow', [
                'slug'=>$row['slug'], 'name'=>$row['name'], 'description'=>$row['description'],
                'industry_id'=>$models['industries'][$row['industry']]->id, 'company_type_id'=>$models['company_types'][$row['company_type']]->id, 'department_id'=>$models['departments'][$row['department']]->id,
            ]);
            foreach ($row['frictions'] as $frictionRow) {
                $patterns = $frictionRow['patterns'];
                unset($frictionRow['patterns']);
                $friction = $this->firstOrRecord($run, FrictionPoint::class, 'friction_point', ['workflow_id'=>$workflow->id] + $frictionRow);
                foreach ($patterns as $patternSlug) $this->attachOrRecord($run, $friction, 'solutionPatterns', $models['solution_patterns'][$patternSlug]);
            }
        }
        foreach ($data['pattern_capabilities'] as $patternSlug => $capabilitySlugs) {
            foreach ($capabilitySlugs as $capabilitySlug) $this->attachOrRecord($run, $models['solution_patterns'][$patternSlug], 'capabilities', $models['capabilities'][$capabilitySlug]);
        }
    }

    private function firstOrRecord(ContentInstallationRun $run, string $class, string $type, array $row): Model
    {
        $model = $class::where('slug', $row['slug'])->first();
        if ($model) { $this->record($run, $type, $model, $row['slug'], 'reused'); return $model; }
        $model = $class::create($row);
        $this->record($run, $type, $model, $row['slug'], 'created');
        return $model;
    }

    private function attachOrRecord(ContentInstallationRun $run, Model $left, string $relation, Model $right): void
    {
        $exists = $left->{$relation}()->where($right->getTable().'.id', $right->id)->exists();
        if (! $exists) $left->{$relation}()->attach($right->id);
        $this->record($run, 'pivot', $left, $left->getTable().':'.$left->id.'->'.$right->getTable().':'.$right->id, $exists ? 'relationship_exists' : 'attached', ['relation'=>$relation, 'related_type'=>$right::class, 'related_id'=>$right->id]);
    }

    private function detachPivot(ContentInstallationItem $item): array
    {
        $model = $item->model_type::find($item->model_id); $meta = $item->metadata ?? [];
        if ($model && isset($meta['relation'], $meta['related_id'])) $model->{$meta['relation']}()->detach($meta['related_id']);
        $item->update(['action' => 'detached']);
        return ['action'=>'detached', 'stable_key'=>$item->stable_key];
    }

    private function removeCreatedModel(ContentInstallationItem $item): array
    {
        $model = $item->model_type::find($item->model_id);
        if (! $model) return ['action'=>'missing', 'stable_key'=>$item->stable_key];
        if ($model instanceof AssessmentQuestion && $model->responses()->exists()) { $model->update(['is_active'=>false]); $item->update(['action'=>'deactivated']); return ['action'=>'deactivated', 'stable_key'=>$item->stable_key]; }
        if ($this->hasDependencies($model)) { $item->update(['action'=>'retained']); return ['action'=>'retained', 'stable_key'=>$item->stable_key]; }
        $model->delete(); $item->update(['action'=>'deleted']); return ['action'=>'deleted', 'stable_key'=>$item->stable_key];
    }

    private function hasDependencies(Model $model): bool
    {
        return match (true) {
            $model instanceof Industry => $model->workflows()->exists() || $model->companyTypes()->exists(),
            $model instanceof CompanyType => $model->workflows()->exists() || $model->departments()->exists(),
            $model instanceof Department => $model->workflows()->exists(),
            $model instanceof Workflow => $model->frictionPoints()->exists(),
            $model instanceof FrictionPoint => $model->solutionPatterns()->exists(),
            $model instanceof SolutionPattern => $model->frictionPoints()->exists() || $model->capabilities()->exists(),
            $model instanceof Capability => $model->solutionPatterns()->exists(),
            default => false,
        };
    }

    private function createdByInstaller(Model $model): bool
    { return ContentInstallationItem::where('model_type', $model::class)->where('model_id', $model->id)->whereIn('action', ['created','deactivated'])->exists(); }

    private function record(ContentInstallationRun $run, string $type, ?Model $model, string $key, string $action, array $metadata = []): void
    { $run->items()->create(['item_type'=>$type, 'model_type'=>$model ? $model::class : null, 'model_id'=>$model?->id, 'stable_key'=>$key, 'action'=>$action, 'metadata'=>$metadata]); }

    private function assertDataset(string $dataset): void
    { if (! in_array($dataset, self::DATASETS, true)) throw new RuntimeException('Dataset must be one of: '.implode(', ', self::DATASETS)); }

    private function assessmentQuestions(): array
    {
        $help = 'Use your current operating reality, not an ideal future state.';
        return [
            ['key'=>'workflow_documentation','category'=>'Workflow documentation','question'=>'Do you have clearly documented workflows?','help_text'=>$help,'sort_order'=>1],
            ['key'=>'data_readiness','category'=>'Data readiness','question'=>'Is your operational data organized and accessible?','help_text'=>$help,'sort_order'=>2],
            ['key'=>'pilot_selection','category'=>'Pilot selection','question'=>'Can your team define measurable success for an AI or automation pilot?','help_text'=>$help,'sort_order'=>3],
            ['key'=>'stakeholder_alignment','category'=>'Stakeholder alignment','question'=>'Do process owners have time to support implementation?','help_text'=>$help,'sort_order'=>4],
        ];
    }

    private function atlasRows(): array
    {
        return require base_path('app/Support/GarciaContent/atlas.php');
    }
}
