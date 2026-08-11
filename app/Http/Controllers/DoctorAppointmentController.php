<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\ClinicHoliday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DoctorAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $doctor = Auth::user()->doctor;

        $upcoming = $doctor->appointments()
            ->with(['patient.user'])
            ->where('date_heure_debut', '>=', now())
            ->orderBy('date_heure_debut')
            ->get();

        $past = $doctor->appointments()
            ->with(['patient.user'])
            ->where('date_heure_debut', '<', now())
            ->orderByDesc('date_heure_debut')
            ->get();

        $month = $request->input('month', now()->format('Y-m'));
        $monthStart = Carbon::parse($month . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $calendarAppointments = $doctor->appointments()
            ->with(['patient.user'])
            ->whereBetween('date_heure_debut', [$monthStart, $monthEnd])
            ->orderBy('date_heure_debut')
            ->get()
            ->groupBy(fn ($appointment) => Carbon::parse($appointment->date_heure_debut)->format('Y-m-d'));

        $offDays = $this->buildOffDays($doctor, $monthStart, $monthEnd);

        $daysInMonth = $monthStart->daysInMonth;
        $startOffset = $monthStart->dayOfWeekIso - 1;

        $previousMonth = $monthStart->copy()->subMonth()->format('Y-m');
        $nextMonth = $monthStart->copy()->addMonth()->format('Y-m');

        return view('doctor.appointments.index', compact(
            'upcoming', 'past',
            'monthStart', 'calendarAppointments', 'offDays', 'daysInMonth', 'startOffset', 'previousMonth', 'nextMonth'
        ));
    }

    /**
     * For each day in range, work out if it's a clinic holiday or a
     * personal absence, so the calendar can shade it red.
     */
    private function buildOffDays($doctor, Carbon $monthStart, Carbon $monthEnd): array
    {
        $offDays = [];

        for ($date = $monthStart->copy(); $date->lte($monthEnd); $date->addDay()) {
            $dateString = $date->format('Y-m-d');

            $isHoliday = ClinicHoliday::where('date_debut', '<=', $dateString)
                ->where('date_fin', '>=', $dateString)
                ->exists();

            $isAbsent = $doctor->absences()
                ->where('date_debut', '<=', $dateString)
                ->where('date_fin', '>=', $dateString)
                ->exists();

            if ($isHoliday) {
                $offDays[$dateString] = 'holiday';
            } elseif ($isAbsent) {
                $offDays[$dateString] = 'absence';
            }
        }

        return $offDays;
    }

    public function confirm(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        abort_unless($appointment->doctor_id === $doctor->id, 403);

        $appointment->update(['statut' => 'confirme']);

        return redirect()->route('doctor.appointments.index')->with('status', 'Appointment confirmed.');
    }

    public function cancel(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        abort_unless($appointment->doctor_id === $doctor->id, 403);

        $appointment->update(['statut' => 'annule']);

        return redirect()->route('doctor.appointments.index')->with('status', 'Appointment cancelled.');
    }

    public function complete(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        abort_unless($appointment->doctor_id === $doctor->id, 403);

        $appointment->update(['statut' => 'termine']);

        return redirect()->route('doctor.appointments.index')->with('status', 'Appointment marked as done.');
    }

    public function markAbsent(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        abort_unless($appointment->doctor_id === $doctor->id, 403);

        $appointment->update(['statut' => 'absent']);

        return redirect()->route('doctor.appointments.index')->with('status', 'Patient marked as absent.');
    }
}