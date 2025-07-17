<?php

namespace App\Models\Requirements;

use App\Models\CCriteria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CRequirement extends Model
{
        use HasFactory, Notifiable;

        protected $fillable = [
        'c_criteria_id',
        'requirement_description',
        'point_value',
    ];

    public function cCriteria()
    {
        return $this->belongsTo(CCriteria::class, 'c_criteria_id');
    }
}
