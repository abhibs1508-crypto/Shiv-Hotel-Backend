<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Menu;
use App\Models\Booking;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    // Fetch Rooms for React
    public function getRooms()
    {
        $rooms = Room::all()->map(function ($room) {
            $room->image = $room->image ? asset('storage/' . $room->image) : null;
            return $room;
        });
        return response()->json($rooms);
    }

    // Fetch Menu Items for React
    public function getMenu()
    {
        $menu = Menu::where('is_available', true)->get()->map(function ($item) {
            $item->image = $item->image ? asset('storage/' . $item->image) : null;
            return $item;
        });
        return response()->json($menu);
    }

    // Handle Bookings from React Form
    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
        ]);

        $booking = Booking::create($validated);

        return response()->json(['message' => 'Booking successful!', 'booking' => $booking], 201);
    }
}