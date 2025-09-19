<?php

namespace App\Models\Evaluation;

use App\Models\Nominee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class UserNomineeStatus extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'nominee_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function nominee()
    {
        return $this->belongsTo(Nominee::class, 'nominee_id');
    }
}
