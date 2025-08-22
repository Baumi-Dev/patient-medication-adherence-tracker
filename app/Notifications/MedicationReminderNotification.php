<?php

namespace App\Notifications;

use App\Models\AdherenceLog;
use App\Models\Medication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class MedicationReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Medication $medication,
        public AdherenceLog $adherenceLog
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $confirmUrl = URL::temporarySignedRoute(
            'medication.confirm',
            now()->addHours(24),
            ['log' => $this->adherenceLog->id]
        );

        return (new MailMessage)
            ->subject('Medication Reminder: '.$this->medication->name)
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('It\'s time to take your medication: '.$this->medication->name)
            ->line('Dose: '.$this->medication->dose)
            ->line('Scheduled time: '.$this->adherenceLog->scheduled_at->format('g:i A'))
            ->action('Confirm I Took It', $confirmUrl)
            ->line('If you have already taken this medication, please click the button above to confirm.')
            ->line('Thank you for staying on track with your medication schedule!');
    }
}
