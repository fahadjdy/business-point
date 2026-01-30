<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Professions
            ['name' => 'Doctor', 'category' => 'profession'],
            ['name' => 'Dentist', 'category' => 'profession'],
            ['name' => 'Lawyer', 'category' => 'profession'],
            ['name' => 'Teacher', 'category' => 'profession'],
            ['name' => 'Engineer', 'category' => 'profession'],
            ['name' => 'Accountant', 'category' => 'profession'],
            ['name' => 'Architect', 'category' => 'profession'],
            ['name' => 'Pharmacist', 'category' => 'profession'],
            ['name' => 'Nurse', 'category' => 'profession'],
            ['name' => 'Veterinarian', 'category' => 'profession'],

            // Skills/Services
            ['name' => 'Plumber', 'category' => 'skill'],
            ['name' => 'Electrician', 'category' => 'skill'],
            ['name' => 'Carpenter', 'category' => 'skill'],
            ['name' => 'Mechanic', 'category' => 'skill'],
            ['name' => 'Painter', 'category' => 'skill'],
            ['name' => 'Gardener', 'category' => 'skill'],
            ['name' => 'Cleaner', 'category' => 'skill'],
            ['name' => 'Driver', 'category' => 'skill'],
            ['name' => 'Security Guard', 'category' => 'skill'],
            ['name' => 'Handyman', 'category' => 'skill'],
            ['name' => 'Barber', 'category' => 'skill'],
            ['name' => 'Chef', 'category' => 'skill'],
            ['name' => 'Photographer', 'category' => 'skill'],
            ['name' => 'Musician', 'category' => 'skill'],
            ['name' => 'Tutor', 'category' => 'skill'],

            // Business Types
            ['name' => 'Grocery Store', 'category' => 'business'],
            ['name' => 'Restaurant', 'category' => 'business'],
            ['name' => 'Pharmacy', 'category' => 'business'],
            ['name' => 'Hardware Store', 'category' => 'business'],
            ['name' => 'Clothing Store', 'category' => 'business'],
            ['name' => 'Electronics Shop', 'category' => 'business'],
            ['name' => 'Bookstore', 'category' => 'business'],
            ['name' => 'Bakery', 'category' => 'business'],
            ['name' => 'Gas Station', 'category' => 'business'],
            ['name' => 'Bank', 'category' => 'business'],
            ['name' => 'Hospital', 'category' => 'business'],
            ['name' => 'School', 'category' => 'business'],
            ['name' => 'Gym', 'category' => 'business'],
            ['name' => 'Salon', 'category' => 'business'],
            ['name' => 'Auto Repair', 'category' => 'business'],

            // Services
            ['name' => 'Home Delivery', 'category' => 'service'],
            ['name' => 'Catering', 'category' => 'service'],
            ['name' => 'Cleaning Service', 'category' => 'service'],
            ['name' => 'Taxi Service', 'category' => 'service'],
            ['name' => 'Courier Service', 'category' => 'service'],
            ['name' => 'Repair Service', 'category' => 'service'],
            ['name' => 'Consultation', 'category' => 'service'],
            ['name' => 'Emergency Service', 'category' => 'service'],
            ['name' => 'Installation', 'category' => 'service'],
            ['name' => 'Maintenance', 'category' => 'service'],
        ];

        foreach ($tags as $tagData) {
            Tag::create([
                'name' => $tagData['name'],
                'slug' => Str::slug($tagData['name']),
                'category' => $tagData['category'],
                'is_active' => true,
            ]);
        }
    }
}