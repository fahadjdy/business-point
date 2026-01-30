<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Doctor', 'Plumber', 'Electrician', 'Carpenter', 'Mechanic',
            'Teacher', 'Lawyer', 'Accountant', 'Engineer', 'Designer',
            'Barber', 'Chef', 'Photographer', 'Musician', 'Artist',
            'Grocery Store', 'Restaurant', 'Pharmacy', 'Hardware Store',
            'Clothing Store', 'Electronics Shop', 'Bookstore', 'Bakery',
            'Gas Station', 'Bank', 'Hospital', 'School', 'Library'
        ]) . ' ' . fake()->randomNumber(3);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'category' => fake()->randomElement(['profession', 'skill', 'business', 'service']),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the tag is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the tag is for a specific category.
     */
    public function category(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }
}