<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Room;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =========================================
        // 1. CREATE THE MASTER ADMIN
        // =========================================
        User::updateOrCreate(
            ['email' => 'admin@shivhotels.com'], 
            [
                'name' => 'Abhishek Solanki',
                'password' => Hash::make('password123'), 
            ]
        );

        // =========================================
        // 2. CREATE MENU CATEGORIES
        // =========================================
        $categories = [
            ['name' => 'Small Plates', 'slug' => 'small-plates'],
            ['name' => 'Soups', 'slug' => 'soups'],
            ['name' => 'Salads & Accompaniments', 'slug' => 'salads-accompaniments'],
            ['name' => 'Tandoor', 'slug' => 'tandoor'],
            ['name' => 'Global Mains', 'slug' => 'global-mains'],
            ['name' => 'Desserts', 'slug' => 'desserts'],
            ['name' => 'Mocktails / Beverages', 'slug' => 'mocktails-beverages'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], ['name' => $cat['name']]);
        }

        // =========================================
        // 3. CREATE SIGNATURE SUITES (ROOMS)
        // =========================================
        $rooms = [
            [
                'room_number' => '101',
                'type' => 'Ocean View King',
                'price' => 8500,
                'status' => 'Available',
                'description' => 'Panoramic vistas of the coastal ridge, featuring a private balcony and deep soaking tub.',
                'image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1500&q=80',
            ],
            [
                'room_number' => '707',
                'type' => 'Presidential Suite',
                'price' => 25000,
                'status' => 'Available',
                'description' => 'An expansive living area designed for dignitaries. Includes a personalized butler and premium bar.',
                'image' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1500&q=80',
            ]
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(['room_number' => $room['room_number']], $room);
        }

        // =========================================
        // 4. CREATE LUXURY MENU ITEMS
        // =========================================
        // Fetch IDs dynamically to prevent foreign key crashes
        $smallPlatesId = Category::where('slug', 'small-plates')->first()->id ?? 1;
        $soupsId = Category::where('slug', 'soups')->first()->id ?? 2;
        $mainsId = Category::where('slug', 'global-mains')->first()->id ?? 5;
        $drinksId = Category::where('slug', 'mocktails-beverages')->first()->id ?? 7;

        $menuItems = [
            [
                'category_id' => $smallPlatesId,
                'name' => 'Truffle & Parmesan Arancini',
                'price' => 850,
                'description' => 'Crispy risotto balls infused with black truffle and aged parmesan.',
                'image' => 'https://images.unsplash.com/photo-1541529086526-db283c563270?auto=format&fit=crop&w=800&q=80',
                'is_available' => true,
                'is_signature' => false,
            ],
            [
                'category_id' => $soupsId,
                'name' => 'Wild Mushroom Consommé',
                'price' => 550,
                'description' => 'Slow-simmered forest mushrooms with a touch of white truffle oil.',
                'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=800&q=80',
                'is_available' => true,
                'is_signature' => false,
            ],
            [
                'category_id' => $mainsId,
                'name' => 'Kashmiri Morel (Guchi) Pulao',
                'price' => 1850,
                'description' => 'Himalayan wild mushrooms slow-cooked with basmati and saffron threads.',
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80',
                'is_available' => true,
                'is_signature' => true,
            ],
            [
                'category_id' => $drinksId,
                'name' => 'Signature Botanical Gin Mocktail',
                'price' => 450,
                'description' => 'A refreshing non-alcoholic blend of elderflower, citrus, and botanicals.',
                'image' => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&w=800&q=80',
                'is_available' => true,
                'is_signature' => false,
            ]
        ];

        foreach ($menuItems as $item) {
            Menu::firstOrCreate(['name' => $item['name']], $item);
        }
    }
}