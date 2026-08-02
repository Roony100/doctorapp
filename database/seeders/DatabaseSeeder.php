<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 5 specialties
        $specialtyNames = ['Cardiology', 'Dentistry', 'Dermatology', 'Pediatrics', 'General Medicine'];
        $specialties = collect($specialtyNames)->map(function ($name) {
            return Specialty::firstOrCreate(
                ['libelle' => $name],
                ['description' => $name . ' department']
            );
        });

        // 10 doctors
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => 'Dr. ' . fake()->firstName() . ' ' . fake()->lastName(),
                'email' => 'doctor' . $i . '@test.com',
                'password' => Hash::make('password'),
                'role' => 'doctor',
            ]);

            Doctor::create([
                'user_id' => $user->id,
                'specialty_id' => $specialties->random()->id,
                'numero_ordre' => 'DOC-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'duree_consultation' => 30,
                'tarif' => fake()->numberBetween(5000, 20000),
            ]);
        }

        // 30 patients
        for ($i = 1; $i <= 30; $i++) {
            $user = User::create([
                'name' => fake()->firstName() . ' ' . fake()->lastName(),
                'email' => 'patient' . $i . '@test.com',
                'password' => Hash::make('password'),
                'role' => 'patient',
            ]);

            Patient::create([
                'user_id' => $user->id,
                'date_naissance' => fake()->date('Y-m-d', '2005-01-01'),
                'sexe' => fake()->randomElement(['M', 'F']),
                'adresse' => fake()->address(),
                'groupe_sanguin' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
            ]);
        }
    }
}