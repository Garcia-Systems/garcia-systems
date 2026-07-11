<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('contact:mail-diagnostics', function () {
    $mailer = config('mail.default');
    $mailerConfig = config("mail.mailers.{$mailer}", []);
    $internalRecipient = config('mail.lead_notification_email');
    $latestSubmission = \Illuminate\Support\Facades\Schema::hasTable('contact_submissions')
        ? \App\Models\ContactSubmission::query()->with('lead')->latest('id')->first()
        : null;

    $maskEmail = function (?string $email): string {
        if (blank($email) || ! str_contains($email, '@')) {
            return '(not set)';
        }

        [$local, $domain] = explode('@', $email, 2);
        $first = str($local)->substr(0, 1)->toString();

        return $first.'***@'.$domain;
    };

    $tableCount = function (string $table): string {
        if (! \Illuminate\Support\Facades\Schema::hasTable($table)) {
            return 'table missing';
        }

        return (string) \Illuminate\Support\Facades\DB::table($table)->count();
    };

    $this->components->twoColumnDetail('mail.default', (string) $mailer);
    $this->components->twoColumnDetail('queue.default', (string) config('queue.default'));
    $this->components->twoColumnDetail('mail host', (string) data_get($mailerConfig, 'host', '(not configured)'));
    $this->components->twoColumnDetail('mail port', (string) data_get($mailerConfig, 'port', '(not configured)'));
    $this->components->twoColumnDetail('mail encryption', (string) (data_get($mailerConfig, 'encryption') ?: '(not configured)'));
    $this->components->twoColumnDetail('configured from address', (string) config('mail.from.address'));
    $this->components->twoColumnDetail('LEAD_NOTIFICATION_EMAIL set', filled(env('LEAD_NOTIFICATION_EMAIL')) ? 'yes' : 'no');
    $this->components->twoColumnDetail('masked internal recipient', $maskEmail($internalRecipient));
    $this->components->twoColumnDetail('jobs table count', $tableCount('jobs'));
    $this->components->twoColumnDetail('failed_jobs table count', $tableCount('failed_jobs'));
    $this->components->twoColumnDetail('latest contact submission ID', $latestSubmission?->id ? (string) $latestSubmission->id : '(none)');
    $this->components->twoColumnDetail('latest contact submission created_at', $latestSubmission?->created_at?->toDateTimeString() ?? '(none)');
    $this->components->twoColumnDetail('latest lead ID', $latestSubmission?->lead?->id ? (string) $latestSubmission->lead->id : '(none)');
    $this->components->twoColumnDetail('internal contact mail notification', \App\Notifications\LeadSubmitted::class);
    $this->components->twoColumnDetail('confirmation contact mail notification', \App\Notifications\ContactSubmissionReceived::class);

    return self::SUCCESS;
})->purpose('Display safe contact mail workflow diagnostics without sending email');
