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
        $doctors = Doctor::with(['user', 'specialty'])->get();
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $specialties = Specialty::orderBy('libelle')->get();
        return view('admin.doctors.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'specialty_id' => ['required', 'exists:specialties,id'],
            'numero_ordre' => ['required', 'string', 'max:255'],
            'duree_consultation' => ['required', 'integer', 'min:5'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => $user->id,
            'specialty_id' => $validated['specialty_id'],
            'numero_ordre' => $validated['numero_ordre'],
            'duree_consultation' => $validated['duree_consultation'],
        ]);

        return redirect()->route('admin.doctors.index')->with('status', 'Doctor account created.');
    }
}