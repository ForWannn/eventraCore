<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'is_working_day',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'is_working_day' => 'boolean',
    ];
}
