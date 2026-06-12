<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyItem extends Model
{
    protected $fillable = [
        'weekly_report_id', 
        'type', 'content', 
        'is_completed'
    ];
}