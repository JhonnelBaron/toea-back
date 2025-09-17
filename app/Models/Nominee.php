<?php

namespace App\Models;

use App\Models\Evaluation\BroScore;
use App\Models\Evaluation\BroSummary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Nominee extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'nominee_type',
        'nominee_category',
        'region',
        'province',
        'nominee_name',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function broScores()
    {
        return $this->hasMany(BroScore::class, 'nominee_id');
    }

    public function broSummary()
    {
        return $this->hasOne(BroSummary::class, 'nominee_id');
    }

}
