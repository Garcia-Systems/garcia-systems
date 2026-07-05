<?php

namespace App\Notifications;

use App\Models\Assessment;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssessmentSubmitted extends Notification
{
    use Queueable;

    public function __construct(public Assessment $assessment, public ?Lead $lead = null)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('New Garcia Systems AI assessment submission')
            ->greeting('New AI assessment submitted')
            ->line('A visitor completed the AI Readiness Assessment.')
            ->line('Name: '.($this->assessment->name ?: 'Not provided'))
            ->line('Email: '.($this->assessment->email ?: 'Not provided'))
            ->line('Company: '.($this->assessment->company ?: 'Not provided'))
            ->line('Score: '.$this->assessment->score)
            ->line('Tier: '.($this->assessment->result_tier ?: 'Not available'));

        if ($this->lead) {
            $message->action('View lead', route('admin.leads.show', $this->lead));
        }

        return $message;
    }
}
