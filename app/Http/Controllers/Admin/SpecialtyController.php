<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::orderBy('libelle')->get();
        return view('admin.specialties.index', compact('specialties'));
    }

    public function create()
    {
        return view('admin.specialties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'libelle' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Specialty::create($validated);

        return redirect()->route('admin.specialties.index')->with('status', 'Specialty added.');
    }

    public function edit(Specialty $specialty)
    {
        return view('admin.specialties.edit', compact('specialty'));
    }

    public function update(Request $request, Specialty $specialty)
    {
        $validated = $request->validate([
            'libelle' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $specialty->update($validated);

        return redirect()->route('admin.specialties.index')->with('status', 'Specialty updated.');
    }

    public function destroy(Specialty $specialty)
    {
        $specialty->delete();

        return redirect()->route('admin.specialties.index')->with('status', 'Specialty deleted.');
    }
}