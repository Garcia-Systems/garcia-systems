<?php

namespace App\Notifications;

use App\Models\ContactSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactSubmissionReceived extends Notification
{
    use Queueable;

    public function __construct(public ContactSubmission $submission)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('We received your Garcia Systems inquiry')
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->view('mail.leads.contact-confirmation', [
                'submission' => $this->submission,
                'displayName' => $this->displayName(),
            ]);
    }

    private function displayName(): string
    {
        $firstName = str($this->submission->name)->trim()->before(' ')->toString();

        return $firstName !== '' ? $firstName : $this->submission->name;
    }
}
