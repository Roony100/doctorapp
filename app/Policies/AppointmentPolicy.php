<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    /**
     * Doctor actions - the logged-in user's linked doctor record
     * must match the appointment's doctor.
     */
    public function manageAsDoctor(User $user, Appointment $appointment): bool
    {
        return $user->doctor && $appointment->doctor_id === $user->doctor->id;
    }

    /**
     * Patient actions - the logged-in user's linked patient record
     * must match the appointment's patient.
     */
    public function manageAsPatient(User $user, Appointment $appointment): bool
    {
        return $user->patient && $appointment->patient_id === $user->patient->id;
    }
}