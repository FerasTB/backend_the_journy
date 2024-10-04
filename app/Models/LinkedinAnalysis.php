<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkedinAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'overall_score',
    ];
}
