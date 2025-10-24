<?php

namespace App\Models\Evaluation;

use App\Models\Nominee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class BroSummary extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'nominee_id',
        'endorse_externals',
        'final_score',
        'bro_total',
        'bro_a',
        'bro_b',
        'bro_c',
        'bro_d',
        'bro_e',
        'ex1_total',
        'ex1_a',
        'ex1_b',
        'ex1_c',
        'ex1_d',
        'ex1_e',
        'ex2_total',
        'ex2_a',
        'ex2_b',
        'ex2_c',
        'ex2_d',
        'ex2_e',
        'ex3_total',
        'ex3_a',
        'ex3_b',
        'ex3_c',
        'ex3_d',
        'ex3_e'
    ];

    public function nominee()
    {
        return $this->belongsTo(Nominee::class, 'nominee_id');
    }

    public function broScores()
    {
        return $this->hasMany(BroScore::class, 'bro_summmary_id');
    }
}
