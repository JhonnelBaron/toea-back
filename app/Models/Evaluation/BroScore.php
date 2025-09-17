<?php

namespace App\Models\Evaluation;

use App\Models\Nominee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class BroScore extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'bro_summmary_id',
        'user_id',
        'nominee_id',
        'score',
        'remarks',
        'criteria_table',
        'criteria_id',
        'attachment_path',
        'attachment_name',
        'attachment_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function nominee()
    {
        return $this->belongsTo(Nominee::class, 'nominee_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'bro_score_id');
    }

    public function broSummary()
    {
        return $this->belongsTo(BroSummary::class, 'bro_summmary_id');
    }
}
