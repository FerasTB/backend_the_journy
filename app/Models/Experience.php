<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'exper_name', 
        'exper_start_date', 
        'exper_end_date', 
        'company_name', 
        'company_location'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responsibilities()
    {
        return $this->hasMany(Responsibility::class);
    }
}
