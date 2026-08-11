<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PatientAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $patient = Auth::user()->patient;

        $upcoming = $patient->appointments()
            ->with(['doctor.user', 'doctor.specialty'])
            ->where('date_heure_debut', '>=', now())
            ->orderBy('date_heure_debut')
            ->get();

        $past = $patient->appointments()
            ->with(['doctor.user', 'doctor.specialty'])
            ->where('date_heure_debut', '<', now())
            ->orderByDesc('date_heure_debut')
            ->get();

        $month = $request->input('month', now()->format('Y-m'));
        $monthStart = Carbon::parse($month . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $calendarAppointments = $patient->appointments()
            ->with(['doctor.user'])
            ->whereBetween('date_heure_debut', [$monthStart, $monthEnd])
            ->orderBy('date_heure_debut')
            ->get()
            ->groupBy(fn ($appointment) => Carbon::parse($appointment->date_heure_debut)->format('Y-m-d'));

        $daysInMonth = $monthStart->daysInMonth;
        $startOffset = $monthStart->dayOfWeekIso - 1;

        $previousMonth = $monthStart->copy()->subMonth()->format('Y-m');
        $nextMonth = $monthStart->copy()->addMonth()->format('Y-m');

        return view('patient.appointments.index', compact(
            'upcoming', 'past',
            'monthStart', 'calendarAppointments', 'daysInMonth', 'startOffset', 'previousMonth', 'nextMonth'
        ));
    }

    public function cancel(Appointment $appointment)
    {
        $this->authorize('manageAsPatient', $appointment);
        abort_unless(in_array($appointment->statut, ['en_attente', 'confirme']), 403, 'This appointment cannot be cancelled.');
        abort_if(Carbon::parse($appointment->date_heure_debut)->isPast(), 403, 'This appointment has already passed.');

        $appointment->update(['statut' => 'annule']);

        return redirect()->route('patient.appointments.index')->with('status', 'Appointment cancelled.');
    }
}