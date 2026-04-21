<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use Illuminate\Http\Request;
use Throwable;

class AdminController
{
    // Fetch ALL bookings in the hotel history
    public function index(Request $request)
    {
        try {
            // Optional: You could add a check here like `if ($request->user()->role !== 'admin') return 403;`
            
            $bookings = Booking::with(['user', 'room'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json($bookings, 200);
        } catch (Throwable $th) {
            return response()->json(['error' => 'Laravel Crash: ' . $th->getMessage()], 500);
        }
    }

    // Manipulate / Update the status of a specific booking
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|string'
            ]);

            $booking = Booking::find($id);
            
            if (!$booking) {
                return response()->json(['error' => 'Folio not found'], 404);
            }

            $booking->status = $request->status;
            $booking->save();

            return response()->json(['message' => 'Status updated successfully', 'booking' => $booking], 200);
            
        } catch (Throwable $th) {
            return response()->json(['error' => 'Laravel Crash: ' . $th->getMessage()], 500);
        }
    }
}