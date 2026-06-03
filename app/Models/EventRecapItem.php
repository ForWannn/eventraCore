<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRecapItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'date',
        'category',
        'vendor',
        'nominal',
        'description',
        'receipt_path',
        'uploader_id',
    ];

    protected $casts = [
        'date' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
