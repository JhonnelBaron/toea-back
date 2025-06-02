<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject //, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_type',
        'first_name',
        'last_name',
        'designation',
        'position',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

        public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        $nominees = $this->nominee;

        return [
            'user_type' => $this->user_type,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'designation' => $this->designation,
            'position' => $this->position,

            'nominees' => $nominees->map(function ($nominee) {
            return [
                'nominee_type' => $nominee->nominee_type,
                'nominee_category' => $nominee->nominee_category,
                'region' => $nominee->region,
                'province' => $nominee->province,
                'nominee_name' => $nominee->nominee_name,
                'status' => $nominee->status,
            ];
        }),
        ];

    }

    public function nominee()
    {
        return $this->hasMany(Nominee::class, 'user_id');
    }
}
