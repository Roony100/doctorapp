<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    /**
     * Show the logged-in doctor's weekly availabilities.
     */
    public function index()
    {
        $doctor = Auth::user()->doctor;

        $availabilities = $doctor->availabilities()->orderBy('jour_semaine')->get();
        $absences = $doctor->absences()->orderBy('date_debut')->get();

        return view('doctor.availabilities.index', compact('availabilities', 'absences'));
    }

    /**
     * Store a new availability slot.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jour_semaine' => ['required', 'integer', 'between:1,7'],
            'heure_debut'  => ['required', 'date_format:H:i'],
            'heure_fin'    => ['required', 'date_format:H:i', 'after:heure_debut'],
        ]);

        $doctor = Auth::user()->doctor;

        $doctor->availabilities()->create($validated);

        return redirect()->route('doctor.availabilities.index')
            ->with('status', 'Availability added.');
    }

    /**
     * Delete an availability slot.
     */
    public function destroy(int $id)
    {
        $doctor = Auth::user()->doctor;

        $availability = $doctor->availabilities()->findOrFail($id);

        $availability->delete();

        return redirect()->route('doctor.availabilities.index')
            ->with('status', 'Availability removed.');
    }

    /**
     * Show the doctor's dashboard, including which days are "Absent"
     * (no availability set at all for that day).
     */
    public function dashboard()
    {
        $doctor = Auth::user()->doctor;

        $availabilities = $doctor->availabilities()->get();

        $dayNames = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];

        $weekStatus = [];

        foreach ($dayNames as $dayNumber => $dayName) {
            $hasAvailability = $availabilities->contains('jour_semaine', $dayNumber);

            $weekStatus[] = [
                'day_number' => $dayNumber,
                'day_name'   => $dayName,
                'status'     => $hasAvailability ? 'available' : 'absent',
            ];
        }

        return view('doctor.dashboard', compact('weekStatus'));
    }
}