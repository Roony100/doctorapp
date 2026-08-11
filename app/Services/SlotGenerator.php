<?php

namespace App\Services;

use App\Models\Doctor;
use Carbon\Carbon;

class SlotGenerator
{
    public function generate(Doctor $doctor, string $date): array
{
    // Check 0: is this date a clinic-wide holiday?
    $isClinicHoliday = \App\Models\ClinicHoliday::where('date_debut', '<=', $date)
        ->where('date_fin', '>=', $date)
        ->exists();

    if ($isClinicHoliday) {
        return [];
    }

    // Check 1: is the doctor absent this whole day?
    $isAbsent = $doctor->absences()
        ->where('date_debut', '<=', $date)
        ->where('date_fin', '>=', $date)
        ->exists();

    if ($isAbsent) {
        return [];
    }

        $carbonDate = Carbon::parse($date);
        $jourSemaine = $carbonDate->dayOfWeekIso;

        $availabilities = $doctor->availabilities()
            ->where('jour_semaine', $jourSemaine)
            ->where('actif', true)
            ->get();

        $duration = $doctor->duree_consultation;
        $slots = [];

        foreach ($availabilities as $availability) {
            $start = Carbon::parse($date . ' ' . $availability->heure_debut);
            $end = Carbon::parse($date . ' ' . $availability->heure_fin);

            while ($start->copy()->addMinutes($duration)->lte($end)) {
                $slotEnd = $start->copy()->addMinutes($duration);

                // Check 2: is this slot already booked?
                $isBooked = $doctor->appointments()
                    ->whereIn('statut', ['en_attente', 'confirme'])
                    ->where('date_heure_debut', '<', $slotEnd)
                    ->where('date_heure_fin', '>', $start)
                    ->exists();

                // Check 3: is this slot in the past, or too soon (next 10 min)?
                $isTooSoon = $start->lt(now()->addMinutes(10));

                if (!$isBooked && !$isTooSoon) {
                    $slots[] = [
                        'start' => $start->format('H:i'),
                        'end' => $slotEnd->format('H:i'),
                    ];
                }

                $start = $slotEnd;
            }
        }

        return $slots;
    }
}