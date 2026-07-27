<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $fillable = [
        'doctor_id', 'jour_semaine', 'heure_debut', 'heure_fin', 'actif',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
