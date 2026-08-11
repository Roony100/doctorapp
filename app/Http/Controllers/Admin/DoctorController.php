<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with(['user', 'specialties'])->get();
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $specialties = Specialty::orderBy('libelle')->get();
        $nextNumeroOrdre = $this->generateNextNumeroOrdre();
        return view('admin.doctors.create', compact('specialties', 'nextNumeroOrdre'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'specialty_ids' => ['required', 'array', 'min:1'],
            'specialty_ids.*' => ['exists:specialties,id'],
            'duree_consultation' => ['required', 'integer', 'min:5'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'doctor',
        ]);

        $doctor = Doctor::create([
            'user_id' => $user->id,
            'specialty_id' => $validated['specialty_ids'][0],
            'numero_ordre' => $this->generateNextNumeroOrdre(),
            'duree_consultation' => $validated['duree_consultation'],
        ]);

        $doctor->specialties()->sync($validated['specialty_ids']);

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor account created.');
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load('user', 'specialties');
        $specialties = Specialty::orderBy('libelle')->get();
        return view('admin.doctors.edit', compact('doctor', 'specialties'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'specialty_ids' => ['required', 'array', 'min:1'],
            'specialty_ids.*' => ['exists:specialties,id'],
            'numero_ordre' => ['required', 'string', 'max:255'],
            'duree_consultation' => ['required', 'integer', 'min:5'],
        ]);

        $doctor->user->update(['name' => $validated['name']]);

        $doctor->update([
            'specialty_id' => $validated['specialty_ids'][0],
            'numero_ordre' => $validated['numero_ordre'],
            'duree_consultation' => $validated['duree_consultation'],
        ]);

        $doctor->specialties()->sync($validated['specialty_ids']);

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor updated.');
    }

    public function destroy(Doctor $doctor)
    {
        $hasFutureAppointments = $doctor->appointments()
            ->where('date_heure_debut', '>', now())
            ->exists();

        if ($hasFutureAppointments) {
            return redirect()->route('admin.doctors.index')
                ->with('status', 'Cannot delete: this doctor has future appointments.');
        }

        $doctor->user->delete();
        $doctor->delete();

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor deleted.');
    }

    /**
     * Work out the next DOC-XXX number by looking at the highest
     * existing number and adding 1.
     */
    private function generateNextNumeroOrdre(): string
    {
        $lastNumber = Doctor::query()
            ->selectRaw("MAX(CAST(SUBSTRING(numero_ordre, 5) AS UNSIGNED)) as max_number")
            ->value('max_number');

        $nextNumber = ($lastNumber ?? 0) + 1;

        return 'DOC-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}