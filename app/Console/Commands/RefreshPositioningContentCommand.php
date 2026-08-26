<?php

namespace App\Console\Commands;

use App\Support\GarciaContent\PositioningContentRefresher;
use Illuminate\Console\Command;

class RefreshPositioningContentCommand extends Command
{
    protected $signature = 'garcia:refresh-positioning-content {--dry-run : Report changes without writing them}';

    protected $description = 'Safely refresh explicitly managed Garcia Systems positioning/reference content.';

    public function handle(PositioningContentRefresher $refresher): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $summary = $refresher->refresh($dryRun);

        $this->newLine();
        $this->info('Garcia Systems positioning content refresh'.($dryRun ? ' (dry run)' : ''));
        $this->line('Reference records created: '.$summary['created']);
        $this->line('Reference records updated: '.$summary['updated']);
        $this->line('Managed content unchanged: '.$summary['unchanged']);
        $this->line('Protected/customized records skipped: '.$summary['skipped']);
        $this->newLine();
        $this->comment($dryRun ? 'No database changes were made.' : 'No user-created articles or videos were modified.');

        return self::SUCCESS;
    }
}
