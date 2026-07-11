<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('contact:mail-diagnostics', function () {
    $mailer = config('mail.default');
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

    $host = config("mail.mailers.{$mailer}.host") ?: '(not configured)';
    $port = config("mail.mailers.{$mailer}.port") ?: '(not configured)';
    $encryption = config("mail.mailers.{$mailer}.encryption") ?: '(not configured)';

    $this->line('mail.default: '.(string) $mailer);
    $this->line('queue.default: '.(string) config('queue.default'));
    $this->line('Mail host: '.(string) $host);
    $this->line('Mail port: '.(string) $port);
    $this->line('Mail encryption: '.(string) $encryption);
    $this->line('Configured from address: '.(string) config('mail.from.address'));
    $this->line('LEAD_NOTIFICATION_EMAIL configured: '.(filled($internalRecipient) ? 'yes' : 'no'));
    $this->line('Masked internal recipient: '.$maskEmail($internalRecipient));
    $this->line('Jobs table count: '.$tableCount('jobs'));
    $this->line('Failed jobs table count: '.$tableCount('failed_jobs'));
    $this->line('Latest contact submission ID: '.($latestSubmission?->id ? (string) $latestSubmission->id : '(none)'));
    $this->line('Latest contact submission created_at: '.($latestSubmission?->created_at?->toDateTimeString() ?? '(none)'));
    $this->line('Latest lead ID: '.($latestSubmission?->lead?->id ? (string) $latestSubmission->lead->id : '(none)'));
    $this->line('Internal contact mail notification: '.\App\Notifications\LeadSubmitted::class);
    $this->line('Confirmation contact mail notification: '.\App\Notifications\ContactSubmissionReceived::class);

    return self::SUCCESS;
})->purpose('Display safe contact mail workflow diagnostics without sending email');
