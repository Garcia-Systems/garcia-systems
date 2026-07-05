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
            ->subject('New Garcia Systems contact submission')
            ->greeting('New contact submission received')
            ->line('A visitor submitted the Garcia Systems contact form.')
            ->line('Name: '.$this->submission->name)
            ->line('Email: '.$this->submission->email)
            ->line('Company: '.($this->submission->company ?: 'Not provided'))
            ->line('Service interest: '.($this->submission->service_interest ?: 'Not provided'))
            ->line('Message: '.$this->submission->message)
            ->action('View lead', route('admin.leads.show', $this->lead));
    }
}
