<?php

namespace App\Models\Evaluation;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Document extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'bro_score_id',
        'file_name',
        'file_path',
        'file_type',
        'uploaded_by'
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function broScore()
    {
        return $this->belongsTo(BroScore::class, 'bro_score_id');
    }
}
