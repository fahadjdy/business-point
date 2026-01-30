<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'message' => $this->faker->paragraph(3),
            'priority' => $this->faker->randomElement(['normal', 'important']),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_scheduled' => false,
            'scheduled_at' => null,
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_scheduled' => true,
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+1 month'),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'important',
        ]);
    }
}