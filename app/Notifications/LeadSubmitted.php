<?php

namespace App\Notifications;

use App\Models\ContactSubmission;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadSubmitted extends Notification
{
    use Queueable;

    public function __construct(public Lead $lead, public ContactSubmission $submission)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Garcia Systems inquiry from '.$this->submission->name)
            ->replyTo($this->submission->email, $this->submission->name)
            ->view('mail.leads.internal-submitted', [
                'lead' => $this->lead,
                'submission' => $this->submission,
                'leadUrl' => route('admin.leads.show', $this->lead),
            ]);
    }
}
