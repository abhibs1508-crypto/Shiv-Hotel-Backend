<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use App\Models\Booking; 
use Illuminate\Http\Request;
use Throwable; 

class MenuController
{
    // Fetch the menu for the React frontend
    public function index()
    {
        try {
            $menuItems = Menu::with('category')->get()->map(function ($item) {
                
                // Safely extract JUST the string name, not the whole object!
                $categoryName = 'Chef Special';
                if ($item->category) {
                    $categoryName = is_string($item->category) ? $item->category : ($item->category->name ?? 'Chef Special');
                }

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description ?? 'A culinary masterpiece.',
                    'price' => $item->price,
                    
                    // The fixed line:
                    'category' => $categoryName, 
                    
                    'image' => $item->image ? (str_starts_with($item->image, 'http') ? $item->image : asset('storage/' . $item->image)) : null,
                ];
            });
            
            return response()->json($menuItems, 200);

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Laravel Crash: ' . $th->getMessage()], 500);
        }
    }

    // Handle incoming food orders and attach to the room folio
    public function placeOrder(Request $request)
    {
        try {
            $cartItems = $request->input('cartItems');

            if (empty($cartItems)) {
                return response()->json(['error' => 'Your cart is empty.'], 400);
            }

            // 1. Find the guest's ACTIVE room booking
            $booking = Booking::where('user_id', $request->user()->id)
                ->whereIn('status', ['Pending', 'Confirmed', 'Active'])
                ->latest()
                ->first();

            if (!$booking) {
                return response()->json(['error' => 'No active room reservation found to bill this food to!'], 400);
            }

            // 2. Get existing food orders (safely decoding if needed)
            $existingOrders = is_array($booking->dining_orders) 
                ? $booking->dining_orders 
                : (json_decode($booking->dining_orders, true) ?? []);
                
            $foodTotal = 0;

            foreach ($cartItems as $item) {
                $existingOrders[] = [
                    'name' => $item['name'] ?? 'In-Room Dining',
                    'qty' => $item['qty'] ?? 1,
                    'price' => $item['price'] ?? 0
                ];
                $foodTotal += (($item['price'] ?? 0) * ($item['qty'] ?? 1));
            }

            // 3. Save the food to the booking and update the grand total
            $booking->dining_orders = $existingOrders;
            $booking->total_price += $foodTotal;
            $booking->total_amount += $foodTotal;
            $booking->save();

            return response()->json([
                'message' => 'Order successfully added to your Room Folio!'
            ], 201);

        } catch (Throwable $th) {
            return response()->json(['error' => 'Laravel Crash: ' . $th->getMessage()], 500);
        }
    }
}