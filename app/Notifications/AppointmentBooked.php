<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentBooked extends Notification
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
            ->subject('Appointment Request Received')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line("Your appointment request with Dr. {$doctorName} on {$date} has been received.")
            ->line('The doctor will confirm or decline it shortly. We will email you once they respond.')
            ->line('Thank you for using DoctorApp!');
    }
}