<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = [
            [
                'title' => 'Gram Panchayat meeting scheduled for Friday at 10:00 AM in the Community Hall.',
                'message' => 'Important meeting for all village residents regarding community development.',
                'priority' => 'important',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
            ],
            [
                'title' => 'Free Vaccination drive starts tomorrow at the Primary Health Center.',
                'message' => 'All residents are encouraged to participate in the vaccination drive.',
                'priority' => 'normal',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now()->subDays(2),
            ],
            [
                'title' => 'New digital literacy workshop registration now open for all residents.',
                'message' => 'Learn basic computer skills and internet usage in our new workshop.',
                'priority' => 'normal',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now()->subDays(7),
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::updateOrCreate(['title' => $notification['title']], $notification);
        }
    }
}
