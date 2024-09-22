<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name', 
        'last_name', 
        'photo', 
        'email', 
        'password', 
        'phone', 
        'country', 
        'city', 
        'website_url', 
        'linkedin_url'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function info()
    {
        return $this->hasOne(Info::class);
    }


    // public function cv()
    // {
    //     return $this->hasMany(CV::class);
    // }
    public function cv()
    {
        return $this->hasOne(CV::class);
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function references()
    {
        return $this->hasMany(Reference::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function education()
    {
        return $this->hasMany(Education::class);
    }

    public function languages()
    {
        return $this->hasMany(Language::class);
    }

    public function summary()
    {
        return $this->hasOne(Summary::class);
    }
}
