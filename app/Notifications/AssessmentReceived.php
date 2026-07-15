<?php

namespace App\Notifications;

use App\Models\Assessment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssessmentReceived extends Notification
{
    use Queueable;

    public function __construct(public Assessment $assessment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('We received your Garcia Systems AI assessment')
            ->replyTo(config('mail.from.address'), config('mail.from.name'))
            ->view('mail.leads.assessment-confirmation', [
                'assessment' => $this->assessment,
                'displayName' => $this->displayName(),
            ]);
    }

    private function displayName(): string
    {
        $firstName = str($this->assessment->name)->trim()->before(' ')->toString();

        return $firstName !== '' ? $firstName : 'there';
    }
}
