<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id', 'specialty_id', 'numero_ordre',
        'biographie', 'duree_consultation', 'tarif',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialty');
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}