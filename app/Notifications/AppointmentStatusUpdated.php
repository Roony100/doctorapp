<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Appointment $appointment)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $doctorName = $this->appointment->doctor->user->name;
        $date = \Carbon\Carbon::parse($this->appointment->date_heure_debut)->format('D, M j Y - H:i');

        if ($this->appointment->statut === 'confirme') {
            return (new MailMessage)
                ->subject('Appointment Confirmed')
                ->greeting('Hi ' . $notifiable->name . ',')
                ->line("Good news! Dr. {$doctorName} has confirmed your appointment on {$date}.")
                ->line('We look forward to seeing you.');
        }

        return (new MailMessage)
            ->subject('Appointment Declined')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line("Unfortunately, Dr. {$doctorName} was unable to accept your appointment request for {$date}.")
            ->line('Please feel free to book a different time.');
    }
}