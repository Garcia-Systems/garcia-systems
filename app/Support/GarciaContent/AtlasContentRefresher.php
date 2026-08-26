<?php

namespace App\Support\GarciaContent;

use App\Models\{CompanyType, Department, FrictionPoint, Industry, SolutionPattern, Workflow};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class AtlasContentRefresher
{
    /** @return array{workflows_created: int, workflows_updated: int, friction_points_created: int, friction_points_updated: int, relationships_changed: int, unchanged: int, skipped: int} */
    public function refresh(bool $dryRun = false): array
    {
        $summary = [
            'workflows_created' => 0,
            'workflows_updated' => 0,
            'friction_points_created' => 0,
            'friction_points_updated' => 0,
            'relationships_changed' => 0,
            'unchanged' => 0,
            'skipped' => 0,
        ];

        DB::transaction(function () use (&$summary, $dryRun): void {
            foreach (AtlasContent::examples() as $example) {
                $references = $this->references($example);
                $workflowSlug = Str::slug($example['workflow']);
                $workflowDefinition = [
                    'key' => 'atlas.workflow.'.$workflowSlug,
                    'identity' => ['slug' => $workflowSlug],
                    'values' => [
                        'industry_id' => $references['industry']?->id,
                        'company_type_id' => $references['company_type']?->id,
                        'department_id' => $references['department']?->id,
                        'name' => $example['workflow'],
                        'description' => 'An Applied Systems Lab case study for mapping '.$example['workflow'].', clarifying the business question, and comparing practical implementation choices.',
                    ],
                ];

                if (in_array(null, [$references['industry'], $references['company_type'], $references['department']], true)) {
                    $summary['skipped'] += 2;
                    continue;
                }

                [$workflow, $workflowSafe] = $this->reconcile(
                    Workflow::class,
                    $workflowDefinition,
                    'workflows',
                    $summary,
                    $dryRun,
                );

                if (! $workflowSafe) {
                    $summary['skipped']++;
                    continue;
                }

                $frictionDefinition = [
                    'key' => 'atlas.friction-point.'.$example['friction_slug'],
                    'identity' => ['slug' => $example['friction_slug']],
                    'values' => [
                        'workflow_id' => $workflow?->id,
                        'name' => $example['friction'],
                        'description' => $example['friction'].' creates delays, rework, and limited visibility for the team.',
                        'impact' => 'Slower decisions, duplicated effort, avoidable handoffs, and harder coordination.',
                    ],
                ];

                [$friction, $frictionSafe] = $this->reconcile(
                    FrictionPoint::class,
                    $frictionDefinition,
                    'friction_points',
                    $summary,
                    $dryRun,
                );

                if (! $frictionSafe || ! $references['solution_pattern']) {
                    if ($frictionSafe && ! $references['solution_pattern']) {
                        $summary['skipped']++;
                    }
                    continue;
                }

                $alreadyAttached = $friction?->exists
                    && $friction->solutionPatterns()->whereKey($references['solution_pattern']->id)->exists();
                if (! $alreadyAttached) {
                    $summary['relationships_changed']++;
                    if (! $dryRun && $friction?->exists) {
                        // Add only the canonical relationship; never detach administrator-added patterns.
                        $friction->solutionPatterns()->syncWithoutDetaching([$references['solution_pattern']->id]);
                    }
                }
            }
        });

        return $summary;
    }

    /** @return array{0: ?Model, 1: bool} */
    private function reconcile(string $modelClass, array $definition, string $counter, array &$summary, bool $dryRun): array
    {
        $model = $modelClass::query()->where('managed_content_key', $definition['key'])->first();

        if (! $model) {
            if ($modelClass::query()->where($definition['identity'])->exists()) {
                $summary['skipped']++;

                return [null, false];
            }

            $summary[$counter.'_created']++;
            if ($dryRun) {
                return [null, true];
            }

            $model = new $modelClass([...$definition['identity'], ...$definition['values']]);
            $model->forceFill([
                'managed_content_key' => $definition['key'],
                'managed_content_hash' => $this->valuesHash($definition['values']),
            ])->save();

            return [$model, true];
        }

        if (! $this->isUnmodified($model, $definition['values'])) {
            $summary['skipped']++;

            return [$model, false];
        }

        $model->fill($definition['values']);
        $model->managed_content_hash = $this->valuesHash($definition['values']);
        if ($model->isDirty()) {
            $summary[$counter.'_updated']++;
            if (! $dryRun) {
                $model->save();
            }
        } else {
            $summary['unchanged']++;
        }

        return [$model, true];
    }

    private function isUnmodified(Model $model, array $values): bool
    {
        if (! is_string($model->managed_content_hash) || $model->managed_content_hash === '') {
            return false;
        }

        $current = [];
        foreach (array_keys($values) as $attribute) {
            $current[$attribute] = $model->getAttribute($attribute);
        }

        return hash_equals($model->managed_content_hash, $this->valuesHash($current));
    }

    private function valuesHash(array $values): string
    {
        return hash('sha256', json_encode($values, JSON_THROW_ON_ERROR));
    }

    private function references(array $example): array
    {
        return [
            'industry' => Industry::where('slug', Str::slug($example['industry']))->first(),
            'company_type' => CompanyType::where('slug', Str::slug($example['company_type']))->first(),
            'department' => Department::where('slug', Str::slug($example['department']))->first(),
            'solution_pattern' => SolutionPattern::where('slug', Str::slug($example['solution_pattern']))->first(),
        ];
    }
}
