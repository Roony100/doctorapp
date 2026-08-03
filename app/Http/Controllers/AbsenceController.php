<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;

        $absences = $doctor->absences()->orderBy('date_debut')->get();

        return view('doctor.absences.index', compact('absences'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_debut' => ['required', 'date', 'after_or_equal:today'],
            'date_fin'   => ['required', 'date', 'after_or_equal:date_debut'],
            'motif'      => ['nullable', 'string', 'max:255'],
        ]);

        $doctor = Auth::user()->doctor;

        $doctor->absences()->create($validated);

        return redirect()->route('doctor.absences.index')->with('status', 'Absence added.');
    }

    public function destroy(int $id)
    {
        $doctor = Auth::user()->doctor;

        $absence = $doctor->absences()->findOrFail($id);
        $absence->delete();

        return redirect()->route('doctor.absences.index')->with('status', 'Absence removed.');
    }
}