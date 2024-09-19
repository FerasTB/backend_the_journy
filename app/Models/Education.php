<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'university_name', 
        'university_start_date', 
        'university_end_date', 
        'university_location', 
        'specialization'
    ];

    // علاقة مع المستخدم (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
