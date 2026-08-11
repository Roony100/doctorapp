<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicHoliday extends Model
{
    protected $fillable = [
        'date_debut', 'date_fin', 'libelle',
    ];
}