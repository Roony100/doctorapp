<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClinicHoliday;
use Illuminate\Http\Request;

class ClinicHolidayController extends Controller
{
    public function index()
    {
        $holidays = ClinicHoliday::orderBy('date_debut')->get();

        return view('admin.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'libelle' => ['required', 'string', 'max:255'],
        ]);

        ClinicHoliday::create($validated);

        return redirect()->route('admin.holidays.index')->with('status', 'Holiday added.');
    }

    public function destroy(ClinicHoliday $holiday)
    {
        $holiday->delete();

        return redirect()->route('admin.holidays.index')->with('status', 'Holiday removed.');
    }
}