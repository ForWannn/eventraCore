<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventPosition extends Model
{
    protected $fillable = ['event_id', 'name', 'fee'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'event_position_members')
                    ->withPivot('work_dates', 'is_loading', 'is_unloading')
                    ->withTimestamps();
    }
}
