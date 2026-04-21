<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use Illuminate\Http\Request;
use Throwable; 

class BookingController
{
    // 1. Fetch user's bookings
    public function index(Request $request)
    {
        try {
            $bookings = Booking::with('room')->where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->get();
            return response()->json($bookings, 200);
        } catch (Throwable $th) {
            return response()->json(['error' => 'Laravel Crash: ' . $th->getMessage()], 500);
        }
    }

    // 2. Simple Save Booking (Pay at Hotel)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'required|integer|min:1',
                'total_price' => 'required|numeric', 
            ]);

            // Prevent Double Bookings
            $overlappingBooking = Booking::where('room_id', $request->room_id)
                ->whereIn('status', ['Pending', 'Confirmed', 'Active'])
                ->where(function ($query) use ($request) {
                    $query->where('check_in', '<', $request->check_out)
                          ->where('check_out', '>', $request->check_in);
                })->first();

            if ($overlappingBooking) {
                return response()->json(['error' => 'This sanctuary is already reserved for these dates.'], 409);
            }

            // Save the booking
            $booking = Booking::create([
                'user_id' => $request->user()->id,
                'room_id' => $request->room_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'guests' => $request->guests,
                'total_price' => $request->total_price, 
                'total_amount' => $request->total_price, 
                'status' => 'Pending', // Marked as pending until they arrive at the hotel
            ]);

            return response()->json(['message' => 'Reservation Confirmed! Pay upon arrival.', 'booking' => $booking], 201);
            
        } catch (Throwable $th) {
            return response()->json(['error' => 'Laravel Crash: ' . $th->getMessage()], 500);
        }
    }

    // 3. Cancel Booking
    public function destroy($id, Request $request)
    {
        try {
            $booking = Booking::where('id', $id)->where('user_id', $request->user()->id)->first();
            if (!$booking) return response()->json(['error' => 'Booking not found'], 404);
            
            $booking->delete();
            return response()->json(['message' => 'Booking cancelled successfully'], 200);
        } catch (Throwable $th) {
            return response()->json(['error' => 'Laravel Crash: ' . $th->getMessage()], 500);
        }
    }

    // 4. Generate the Invoice/Folio
    public function showInvoice($id, Request $request)
    {
        try {
            $booking = Booking::with(['user', 'room'])->where('id', $id)->where('user_id', $request->user()->id)->first();
            if (!$booking) return response()->json(['error' => 'Folio not found.'], 404);

            $checkIn = \Carbon\Carbon::parse($booking->check_in);
            $checkOut = \Carbon\Carbon::parse($booking->check_out);
            $nights = max(1, $checkIn->diffInDays($checkOut));

            return response()->json([
                'id' => $booking->id,
                'check_in' => $booking->check_in,
                'check_out' => $booking->check_out,
                'nights' => $nights,
                'user' => [
                    'name' => $booking->user->name ?? 'Distinguished Guest',
                    'email' => $booking->user->email ?? 'N/A',
                ],
                'room' => [
                    'type' => $booking->room->type ?? 'Signature Suite',
                    'room_number' => $booking->room->room_number ?? 'TBD',
                    'price' => $booking->room->price ?? 8500,
                ],
                'dining_orders' => is_string($booking->dining_orders) ? json_decode($booking->dining_orders, true) : ($booking->dining_orders ?? [])
            ], 200);

        } catch (Throwable $th) {
            return response()->json(['error' => 'Laravel Crash: ' . $th->getMessage()], 500);
        }
    }
}