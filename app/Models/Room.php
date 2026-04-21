<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number', 
        'type', 
        'description', 
        'price', 
        'capacity', 
        'image', 
        'status', 
        'specs', 
        'amenities'
    ];

    // This is required for Filament to save arrays properly
    protected $casts = [
        'specs' => 'array',
        'amenities' => 'array',
        'price' => 'decimal:2',
        'capacity' => 'integer',
    ];
}