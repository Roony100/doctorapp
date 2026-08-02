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

        return view('doctor.availabilities.index', compact('availabilities'));
    }

    /**
     * Store a new availability slot.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jour_semaine' => ['required', 'integer', 'between:0,6'],
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
}