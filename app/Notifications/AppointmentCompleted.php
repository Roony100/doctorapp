<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentCompleted extends Notification
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

        return (new MailMessage)
            ->subject('Appointment Completed')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line("Your appointment with Dr. {$doctorName} on {$date} has been marked as completed.")
            ->line('Your consultation notes will be available soon, if the doctor adds them.')
            ->line('Thank you for using DoctorApp!');
    }
}