<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContactBook>
 */
class ContactBookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'designation' => fake()->jobTitle(),
            'department' => fake()->randomElement([
                'Sales', 'Marketing', 'Engineering', 'HR', 'Finance',
                'Operations', 'Customer Service', 'IT', 'Legal', 'Admin'
            ]),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'description' => fake()->paragraph(2),
            'type' => fake()->randomElement(['person', 'business', 'service']),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the contact is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the contact is a person.
     */
    public function person(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'person',
            'name' => fake()->name(),
        ]);
    }

    /**
     * Indicate that the contact is a business.
     */
    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'business',
            'name' => fake()->company(),
            'designation' => fake()->randomElement(['Owner', 'Manager', 'Director']),
        ]);
    }

    /**
     * Indicate that the contact is a service.
     */
    public function service(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'service',
            'name' => fake()->company() . ' Services',
        ]);
    }
}