<?php

namespace App\Models\Requirements;

use App\Models\ECriteria;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ERequirement extends Model
{
    use HasFactory, Notifiable;

        protected $fillable = [
        'e_criteria_id',
        'requirement_description',
        'point_value',
    ];

    public function eCriteria()
    {
        return $this->belongsTo(ECriteria::class, 'e_criteria_id');
    }
}
