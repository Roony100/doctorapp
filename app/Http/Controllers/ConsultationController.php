<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    public function edit(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        abort_unless($appointment->doctor_id === $doctor->id, 403);
        abort_unless($appointment->statut === 'termine', 403, 'Notes can only be added once the appointment is marked done.');

        $appointment->load('patient.user', 'consultation');

        return view('doctor.consultation.edit', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        abort_unless($appointment->doctor_id === $doctor->id, 403);
        abort_unless($appointment->statut === 'termine', 403);

        $validated = $request->validate([
            'diagnostic' => ['nullable', 'string'],
            'prescription' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $appointment->consultation()->updateOrCreate(
            ['appointment_id' => $appointment->id],
            $validated
        );

        return redirect()->route('doctor.appointments.index')->with('status', 'Consultation notes saved.');
    }
}