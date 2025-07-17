<?php

namespace App\Models\Requirements;

use App\Models\DCriteria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DRequirement extends Model
{
    use HasFactory, Notifiable;

        protected $fillable = [
        'd_criteria_id',
        'requirement_description',
        'point_value',
    ];

    public function dCriteria()
    {
        return $this->belongsTo(DCriteria::class, 'd_criteria_id');
    }
}
