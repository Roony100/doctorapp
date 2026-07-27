<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'doctor_id', 'date_debut', 'date_fin', 'motif',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}