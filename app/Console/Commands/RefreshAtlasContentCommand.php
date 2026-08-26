<?php

namespace App\Console\Commands;

use App\Support\GarciaContent\AtlasContentRefresher;
use Illuminate\Console\Command;

class RefreshAtlasContentCommand extends Command
{
    protected $signature = 'garcia:refresh-atlas-content {--dry-run : Report changes without writing them}';

    protected $description = 'Safely refresh explicitly managed Garcia Systems Atlas examples.';

    public function handle(AtlasContentRefresher $refresher): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $summary = $refresher->refresh($dryRun);

        $this->newLine();
        $this->info('Garcia Systems Atlas content refresh'.($dryRun ? ' (dry run)' : ''));
        $this->line('Workflows created: '.$summary['workflows_created']);
        $this->line('Workflows updated: '.$summary['workflows_updated']);
        $this->line('Friction points created: '.$summary['friction_points_created']);
        $this->line('Friction points updated: '.$summary['friction_points_updated']);
        $this->line('Relationships changed: '.$summary['relationships_changed']);
        $this->line('Managed content unchanged: '.$summary['unchanged']);
        $this->line('Protected/customized records skipped: '.$summary['skipped']);
        $this->newLine();
        $this->comment($dryRun ? 'No database changes were made.' : 'No articles, videos, or unrelated production content were modified.');

        return self::SUCCESS;
    }
}
