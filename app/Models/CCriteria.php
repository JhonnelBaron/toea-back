<?php

namespace App\Models;

use App\Models\Requirements\CRequirement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CCriteria extends Model
{
    use HasFactory, Notifiable;

     protected $fillable = [
        'number',
        'title',
        'description',
        'means_of_verification',
        'criteria_function',
        'designated_offices',
        'bro_small',
        'bro_medium',
        'bro_large',
        'gp_small',
        'gp_medium',
        'gp_large',
        'bti_rtcstc',
        'bti_ptcdtc',
        'bti_tas'
    ];

    public function cRequirements()
    {
        return $this->hasMany(CRequirement::class, 'c_criteria_id');
    }
}
