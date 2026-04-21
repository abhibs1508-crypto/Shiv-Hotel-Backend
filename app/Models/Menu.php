<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', // Make sure this is here!
        'name',
        'description',
        'price',
        'image',
        'status', // 'available' or 'unavailable'
    ];

    // THIS IS THE MAGIC LINK
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}