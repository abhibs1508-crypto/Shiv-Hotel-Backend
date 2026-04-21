<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id', // <-- Added for Razorpay
        'user_id',
        'room_id',
        'check_in',
        'check_out',
        'guests',
        'total_price',
        'total_amount', 
        'status',
        'dining_orders',
    ];

    protected $casts = [
        'dining_orders' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}