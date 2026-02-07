<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            TagSeeder::class,
            EmergencyContactSeeder::class,            
            NotificationSeeder::class,
            SettingSeeder::class,
        ]);

        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'fahadjdy12@gmail.com',
            'phone' => 'Fahad@123',
        ]);
    }
}
