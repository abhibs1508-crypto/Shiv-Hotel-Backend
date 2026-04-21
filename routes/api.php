<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\MenuController; // <-- Added Menu Controller

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (No Login Required)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'show']);

// <-- PUBLIC MENU ROUTE -->
Route::get('/menu', [MenuController::class, 'index']);

/*
|--------------------------------------------------------------------------
| SECURE VIP ROUTES (Login Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // User Session
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // User Itinerary & Booking
    Route::get('/my-bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::delete('/user/bookings/{id}', [BookingController::class, 'destroy']);
    Route::get('/invoice/{id}', [BookingController::class, 'showInvoice']);

    // <-- SECURE MENU ORDER ROUTE -->
// Find this line:
    // Route::post('/menu/order', [MenuController::class, 'placeOrder']);

    // And change it to exactly this:
    Route::post('/user/dining-orders', [MenuController::class, 'placeOrder']);
    // Manager Routes
    Route::get('/admin/bookings', [AdminController::class, 'index']);
    Route::patch('/admin/bookings/{id}/status', [AdminController::class, 'updateStatus']);
});