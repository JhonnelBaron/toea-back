<?php

namespace App\Models;

use App\Models\Requirements\BRequirement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class BCriteria extends Model
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

    public function bRequirements()
    {
        return $this->hasMany(BRequirement::class, 'b_criteria_id');
    }

        protected static function booted()
    {
        static::deleting(function ($criteria) {
            $criteria->bRequirements()->delete();
        });
    }
}
