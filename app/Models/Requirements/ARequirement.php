<?php

namespace App\Models\Requirements;

use App\Models\ACriteria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ARequirement extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'a_criteria_id',
        'requirement_description',
        'point_value',
    ];

    public function aCriteria()
    {
        return $this->belongsTo(ACriteria::class, 'a_criteria_id');
    }
}
