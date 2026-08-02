<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AvailabilityController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->middleware('checkrole:admin')->name('admin.dashboard');

    Route::get('/doctor/dashboard', function () {
        return view('doctor.dashboard');
    })->middleware('checkrole:doctor')->name('doctor.dashboard');

    Route::get('/patient/dashboard', function () {
        return view('patient.dashboard');
    })->middleware('checkrole:patient')->name('patient.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified', 'checkrole:doctor'])->group(function () {
    Route::get('/doctor/availabilities', [AvailabilityController::class, 'index'])->name('doctor.availabilities.index');
    Route::post('/doctor/availabilities', [AvailabilityController::class, 'store'])->name('doctor.availabilities.store');
    Route::delete('/doctor/availabilities/{id}', [AvailabilityController::class, 'destroy'])->name('doctor.availabilities.destroy');
});

use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\Admin\DoctorController;

Route::middleware(['auth', 'verified', 'checkrole:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('specialties', SpecialtyController::class);
    Route::get('doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
    Route::post('doctors', [DoctorController::class, 'store'])->name('doctors.store');
});
