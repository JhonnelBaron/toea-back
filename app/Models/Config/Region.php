<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Region extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = ['name'];

    public function provinces()
    {
        return $this->hasMany(Province::class, 'region_id');
    }
}
