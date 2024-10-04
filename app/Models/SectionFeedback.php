<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'section_name',
        'original_section_text',
        'notes',
        'advice',
        'enhanced_section_text',
        'score',
    ];

    protected $casts = [
        'notes' => 'array',
        'advice' => 'array',
    ];
}
