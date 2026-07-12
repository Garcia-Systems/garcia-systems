<?php

namespace App\Console\Commands;

use App\Models\ContentInstallationRun;
use App\Support\GarciaContent\StructuredContentInstaller;
use Illuminate\Console\Command;

class GarciaContentCommand extends Command
{
    protected $signature = 'garcia:content {action : install, status, or rollback} {--dataset= : Dataset for install: assessment, atlas, or all} {--run= : Run UUID or numeric ID} {--latest : Roll back latest completed run} {--dry-run : Preview without writing}';
    protected $description = 'Install, inspect, and roll back reversible Garcia Systems structured content.';

    public function handle(StructuredContentInstaller $installer): int
    {
        return match ($this->argument('action')) {
            'install' => $this->install($installer),
            'status' => $this->status($installer),
            'rollback' => $this->rollback($installer),
            default => $this->invalidAction(),
        };
    }

    private function install(StructuredContentInstaller $installer): int
    {
        $dataset = (string) $this->option('dataset');
        if ($dataset === '') { $this->error('Choose an explicit --dataset=assessment, --dataset=atlas, or --dataset=all.'); return self::FAILURE; }
        try { $result = $installer->install($dataset, (bool) $this->option('dry-run')); } catch (\Throwable $e) { $this->error($e->getMessage()); return self::FAILURE; }
        $this->info($this->option('dry-run') ? 'Dry run only. No database changes were made.' : 'Content installation run: '.$result['run']->uuid);
        $this->table(['action','type','key'], collect($result['items'])->map(fn($i)=>[is_array($i)?$i['action']:$i->action, is_array($i)?$i['item_type']:$i->item_type, is_array($i)?$i['stable_key']:$i->stable_key])->all());
        return self::SUCCESS;
    }

    private function status(StructuredContentInstaller $installer): int
    {
        $id = $this->option('run');
        $query = ContentInstallationRun::query()->with('items')->latest('id');
        if ($id) $query->where(fn($q)=>$q->where('uuid', $id)->orWhere('id', $id));
        $runs = $query->take($id ? 1 : 20)->get();
        $this->table(['run','dataset','status','installed','rolled back','created','reused','pivots','deleted','deactivated','retained','appears active'], $runs->map(function ($run) use ($installer) {
            $c = fn($actions) => $run->items->whereIn('action', (array) $actions)->count();
            return [$run->uuid, $run->dataset, $run->status, $run->completed_at?->toDateTimeString(), $run->rolled_back_at?->toDateTimeString() ?: '-', $c('created'), $c(['reused','reactivated']), $c('attached'), $c('deleted'), $c('deactivated'), $c('retained'), $installer->activeState($run->dataset) ? 'yes' : 'no'];
        })->all());
        return self::SUCCESS;
    }

    private function rollback(StructuredContentInstaller $installer): int
    {
        $runId = $this->option('run');
        if (! $runId && ! $this->option('latest')) { $this->error('Provide --run=<run-id> or --latest.'); return self::FAILURE; }
        $run = $runId ? ContentInstallationRun::where(fn($q)=>$q->where('uuid', $runId)->orWhere('id', $runId))->first() : ContentInstallationRun::where('status','completed')->latest('id')->first();
        if (! $run) { $this->error('No matching installation run found.'); return self::FAILURE; }
        try { $events = $installer->rollback($run, (bool) $this->option('dry-run')); } catch (\Throwable $e) { $this->error($e->getMessage()); return self::FAILURE; }
        $this->info(($this->option('dry-run') ? 'Rollback dry run for ' : 'Rolled back ').$run->uuid);
        $this->table(['action','key'], collect($events)->map(fn($e)=>[$e['action'], $e['stable_key'] ?? ''])->all());
        return self::SUCCESS;
    }

    private function invalidAction(): int
    {
        $this->error('Action must be install, status, or rollback.');
        return self::FAILURE;
    }
}
