<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\PatientAppointmentController;
use App\Http\Controllers\DoctorAppointmentController;
use App\Http\Controllers\Admin\ClinicHolidayController;
use App\Http\Controllers\ConsultationController;

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

    Route::get('/doctor/dashboard', [AvailabilityController::class, 'dashboard'])->middleware('checkrole:doctor')->name('doctor.dashboard');

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
    Route::get('/doctor/absences', [AbsenceController::class, 'index'])->name('doctor.absences.index');
    Route::post('/doctor/absences', [AbsenceController::class, 'store'])->name('doctor.absences.store');
    Route::delete('/doctor/absences/{id}', [AbsenceController::class, 'destroy'])->name('doctor.absences.destroy');
    Route::get('/doctor/appointments', [DoctorAppointmentController::class, 'index'])->name('doctor.appointments.index');
    Route::patch('/doctor/appointments/{appointment}/confirm', [DoctorAppointmentController::class, 'confirm'])->name('doctor.appointments.confirm');
    Route::patch('/doctor/appointments/{appointment}/cancel', [DoctorAppointmentController::class, 'cancel'])->name('doctor.appointments.cancel');
    Route::patch('/doctor/appointments/{appointment}/complete', [DoctorAppointmentController::class, 'complete'])->name('doctor.appointments.complete');
    Route::patch('/doctor/appointments/{appointment}/mark-absent', [DoctorAppointmentController::class, 'markAbsent'])->name('doctor.appointments.mark-absent');
    Route::get('/doctor/appointments/{appointment}/consultation', [ConsultationController::class, 'edit'])->name('doctor.consultation.edit');
    Route::post('/doctor/appointments/{appointment}/consultation', [ConsultationController::class, 'store'])->name('doctor.consultation.store');
});

use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\Admin\DoctorController;

Route::middleware(['auth', 'verified', 'checkrole:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('specialties', SpecialtyController::class);
    Route::get('doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
    Route::post('doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::get('doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
    Route::put('doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
    Route::delete('doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
    Route::get('holidays', [ClinicHolidayController::class, 'index'])->name('holidays.index');
    Route::post('holidays', [ClinicHolidayController::class, 'store'])->name('holidays.store');
    Route::delete('holidays/{holiday}', [ClinicHolidayController::class, 'destroy'])->name('holidays.destroy');
});

use App\Http\Controllers\BookingController;

Route::middleware(['auth', 'verified', 'checkrole:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('booking/search', [BookingController::class, 'search'])->name('booking.search');
    Route::get('booking/doctor/{doctor}/slots', [BookingController::class, 'slots'])->name('booking.slots');
    Route::post('booking/doctor/{doctor}/book', [BookingController::class, 'store'])->name('booking.store');
    Route::get('booking/doctor/{doctor}/reschedule/{appointment}', [BookingController::class, 'reschedule'])->name('booking.reschedule');
    Route::get('appointments', [PatientAppointmentController::class, 'index'])->name('appointments.index');
    Route::patch('appointments/{appointment}/cancel', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');
});