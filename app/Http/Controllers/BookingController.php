<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\ClinicHoliday;
use App\Models\Appointment;
use App\Services\SlotGenerator;
use App\Notifications\AppointmentBooked;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function search(Request $request)
    {
        $specialties = Specialty::orderBy('libelle')->get();

        $doctors = collect();

        if ($request->filled('specialty_id')) {
            $doctors = Doctor::with(['user', 'specialty'])
                ->where('specialty_id', $request->specialty_id)
                ->get();
        }

        return view('patient.booking.search', compact('specialties', 'doctors'));
    }

    public function slots(Request $request, Doctor $doctor, SlotGenerator $slotGenerator)
    {
        $doctor->load('user', 'specialty');

        $date = $request->input('date', now()->format('Y-m-d'));

        $slots = $slotGenerator->generate($doctor, $date);

        $month = Carbon::parse($date)->format('Y-m');
        $dayStatuses = $this->buildDayStatuses($doctor, $month, $slotGenerator);

        $monthStart = Carbon::parse($month . '-01')->startOfMonth();
        $daysInMonth = $monthStart->daysInMonth;
        $startOffset = $monthStart->dayOfWeekIso - 1;
        $previousMonth = $monthStart->copy()->subMonth()->format('Y-m');
        $nextMonth = $monthStart->copy()->addMonth()->format('Y-m');

        $rescheduling = null;

        return view('patient.booking.slots', compact(
            'doctor', 'date', 'slots',
            'dayStatuses', 'monthStart', 'daysInMonth', 'startOffset', 'previousMonth', 'nextMonth',
            'rescheduling'
        ));
    }

    public function reschedule(Request $request, Doctor $doctor, Appointment $appointment, SlotGenerator $slotGenerator)
    {
        $this->authorize('manageAsPatient', $appointment);
        abort_unless($appointment->doctor_id === $doctor->id, 403);
        abort_unless(in_array($appointment->statut, ['en_attente', 'confirme']), 403, 'This appointment cannot be rescheduled.');

        $doctor->load('user', 'specialty');

        $date = $request->input('date', now()->format('Y-m-d'));

        $slots = $slotGenerator->generate($doctor, $date);

        $month = Carbon::parse($date)->format('Y-m');
        $dayStatuses = $this->buildDayStatuses($doctor, $month, $slotGenerator);

        $monthStart = Carbon::parse($month . '-01')->startOfMonth();
        $daysInMonth = $monthStart->daysInMonth;
        $startOffset = $monthStart->dayOfWeekIso - 1;
        $previousMonth = $monthStart->copy()->subMonth()->format('Y-m');
        $nextMonth = $monthStart->copy()->addMonth()->format('Y-m');

        $rescheduling = $appointment;

        return view('patient.booking.slots', compact(
            'doctor', 'date', 'slots',
            'dayStatuses', 'monthStart', 'daysInMonth', 'startOffset', 'previousMonth', 'nextMonth',
            'rescheduling'
        ));
    }

    /**
     * Work out a green/red/half status for each day of the given month,
     * based on how many slots are free vs how many could theoretically exist.
     */
    private function buildDayStatuses(Doctor $doctor, string $month, SlotGenerator $slotGenerator): array
    {
        $monthStart = Carbon::parse($month . '-01')->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $statuses = [];

        for ($date = $monthStart->copy(); $date->lte($monthEnd); $date->addDay()) {
            $dateString = $date->format('Y-m-d');

            $isClinicHoliday = ClinicHoliday::where('date_debut', '<=', $dateString)
                ->where('date_fin', '>=', $dateString)
                ->exists();

            $isAbsent = $doctor->absences()
                ->where('date_debut', '<=', $dateString)
                ->where('date_fin', '>=', $dateString)
                ->exists();

            if ($isClinicHoliday || $isAbsent) {
                $statuses[$dateString] = 'red';
                continue;
            }

            $jourSemaine = $date->dayOfWeekIso;
            $totalPossibleSlots = 0;

            $availabilities = $doctor->availabilities()
                ->where('jour_semaine', $jourSemaine)
                ->where('actif', true)
                ->get();

            foreach ($availabilities as $availability) {
                $start = Carbon::parse($dateString . ' ' . $availability->heure_debut);
                $end = Carbon::parse($dateString . ' ' . $availability->heure_fin);
                $totalPossibleSlots += intdiv($start->diffInMinutes($end), $doctor->duree_consultation);
            }

            if ($totalPossibleSlots === 0) {
                $statuses[$dateString] = 'red';
                continue;
            }

            $freeSlots = count($slotGenerator->generate($doctor, $dateString));

            if ($freeSlots === 0) {
                $statuses[$dateString] = 'red';
            } elseif ($freeSlots >= $totalPossibleSlots) {
                $statuses[$dateString] = 'green';
            } else {
                $statuses[$dateString] = 'half';
            }
        }

        return $statuses;
    }

    public function store(Request $request, Doctor $doctor, SlotGenerator $slotGenerator)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'reschedule_appointment_id' => ['nullable', 'integer'],
        ]);

        $patient = Auth::user()->patient;

        $start = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
        $end = $start->copy()->addMinutes($doctor->duree_consultation);

        $oldAppointment = null;

        if (!empty($validated['reschedule_appointment_id'])) {
            $oldAppointment = Appointment::find($validated['reschedule_appointment_id']);

            abort_unless($oldAppointment, 404);
            $this->authorize('manageAsPatient', $oldAppointment);
            abort_unless($oldAppointment->doctor_id === $doctor->id, 403);
        }

        $booked = false;
        $newAppointment = null;

        DB::transaction(function () use ($doctor, $patient, $start, $end, $oldAppointment, &$booked, &$newAppointment) {
            $conflictQuery = $doctor->appointments()
                ->whereIn('statut', ['en_attente', 'confirme'])
                ->where('date_heure_debut', '<', $end)
                ->where('date_heure_fin', '>', $start);

            if ($oldAppointment) {
                $conflictQuery->where('id', '!=', $oldAppointment->id);
            }

            $conflict = $conflictQuery->lockForUpdate()->exists();

            if ($conflict) {
                return;
            }

            if ($oldAppointment) {
                $oldAppointment->update(['statut' => 'annule']);
            }

            $newAppointment = $doctor->appointments()->create([
                'patient_id' => $patient->id,
                'date_heure_debut' => $start,
                'date_heure_fin' => $end,
                'statut' => 'en_attente',
                'created_by' => Auth::id(),
            ]);

            $booked = true;
        });

        if (!$booked) {
            return redirect()
                ->route('patient.booking.slots', ['doctor' => $doctor, 'date' => $validated['date']])
                ->with('status', 'Sorry, that slot was just taken. Please choose another.');
        }

        $newAppointment->load('doctor.user');
        Auth::user()->notify(new AppointmentBooked($newAppointment));

        $message = $oldAppointment ? 'Appointment rescheduled successfully.' : 'Appointment booked successfully.';

        return redirect()
            ->route('patient.dashboard')
            ->with('status', $message);
    }
}