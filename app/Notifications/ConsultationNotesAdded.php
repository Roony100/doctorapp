<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ConsultationNotesAdded extends Notification
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
            ->subject('Consultation Notes Ready')
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line("Dr. {$doctorName} has added notes for your appointment on {$date}.")
            ->line('Log in to your account to view your diagnostic, prescription, and notes.')
            ->line('Thank you for using DoctorApp!');
    }
}