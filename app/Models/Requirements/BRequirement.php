<?php

namespace App\Models\Requirements;

use App\Models\BCriteria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class BRequirement extends Model
{
    use HasFactory, Notifiable;

        protected $fillable = [
        'b_criteria_id',
        'requirement_description',
        'point_value',
    ];

    public function bCriteria()
    {
        return $this->belongsTo(BCriteria::class, 'b_criteria_id');
    }
}
