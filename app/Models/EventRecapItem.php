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
        'item_name',
        'vendor',
        'description',
        'quantity',
        'unit_price',
        'nominal',
        'notes',
        'receipt_path',
        'uploader_id',
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
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