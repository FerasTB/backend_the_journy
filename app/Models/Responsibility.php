<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsibility extends Model
{
    use HasFactory;
    protected $fillable = ['exper_id', 'responsability_name'];

    // علاقة مع الخبرات (Experience)
    public function experience()
    {
        return $this->belongsTo(Experience::class, 'exper_id');
    }
}
