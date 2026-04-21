<?php

namespace App\Http\Controllers\Api;

use App\Models\Room;
use Illuminate\Http\Request;
use Throwable;

class RoomController
{
    public function index()
    {
        try {
            $rooms = Room::all()->map(function ($room) {
                
                // 1. Ultra-Safe Array Parsing
                $safeSpecs = is_string($room->specs) ? json_decode($room->specs, true) : (is_array($room->specs) ? $room->specs : []);
                $safeAmenities = is_string($room->amenities) ? json_decode($room->amenities, true) : (is_array($room->amenities) ? $room->amenities : []);

                // 2. Ultra-Safe Image Parsing
                $imagePath = 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=2500&q=80';
                if (!empty($room->image)) {
                    $img = is_array($room->image) ? ($room->image[0] ?? '') : $room->image;
                    if (is_string($img) && $img !== '') {
                        $imagePath = str_starts_with($img, 'http') ? $img : asset('storage/' . $img);
                    }
                }

                return [
                    'id' => $room->id,
                    'type' => $room->type ?? 'Luxury Suite',
                    'room_number' => $room->room_number ?? 'TBD',
                    'description' => $room->description ?? 'Experience ultimate luxury.',
                    'price' => $room->price ?? 8500,
                    'capacity' => $room->capacity ?? 2,
                    'specs' => $safeSpecs ?: [],
                    'amenities' => $safeAmenities ?: [],
                    'image' => $imagePath,
                ];
            });

            return response()->json($rooms, 200);

        } catch (Throwable $th) {
            return response()->json(['error' => 'Laravel Crash: ' . $th->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $room = Room::find($id);
            if (!$room) return response()->json(['error' => 'Room not found'], 404);

            $safeSpecs = is_string($room->specs) ? json_decode($room->specs, true) : (is_array($room->specs) ? $room->specs : []);
            $safeAmenities = is_string($room->amenities) ? json_decode($room->amenities, true) : (is_array($room->amenities) ? $room->amenities : []);

            $imagePath = null;
            if (!empty($room->image)) {
                $img = is_array($room->image) ? ($room->image[0] ?? '') : $room->image;
                if (is_string($img) && $img !== '') {
                    $imagePath = str_starts_with($img, 'http') ? $img : asset('storage/' . $img);
                }
            }

            return response()->json([
                'id' => $room->id,
                'type' => $room->type,
                'room_number' => $room->room_number,
                'description' => $room->description,
                'price' => $room->price,
                'capacity' => $room->capacity,
                'specs' => $safeSpecs ?: [],
                'amenities' => $safeAmenities ?: [],
                'image' => $imagePath,
            ], 200);

        } catch (Throwable $th) {
            return response()->json(['error' => 'Laravel Crash: ' . $th->getMessage()], 500);
        }
    }
}