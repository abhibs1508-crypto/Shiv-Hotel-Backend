<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Booking;
use App\Models\Room;

class RevenueStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalRevenue = Booking::where('status', 'Checked Out')->sum('total_price');

        $activeBookings = Booking::where('status', 'Active')->count();

        $availableRooms = Room::where('status', 'Available')->count();

        return [
            Stat::make('Total Revenue', '₹ ' . number_format($totalRevenue))
                ->description('Completed bookings only')
                ->color('success'),

            Stat::make('Active Bookings', $activeBookings)
                ->color('primary'),

            Stat::make('Available Rooms', $availableRooms)
                ->color('warning'),
        ];
    }
}