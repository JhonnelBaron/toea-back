<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Province extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['region_id', 'name'];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function institutions()
    {
        return $this->hasMany(Institution::class, 'province_id');
    }
}
