<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', '+1 month');

        return [
            'client_name' => fake()->company(),
            'project_name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['Planning', 'In Progress', 'On Hold', 'Completed']),
            'priority' => fake()->randomElement(['Low', 'Medium', 'High']),
            'start_date' => $startDate,
            'due_date' => fake()->dateTimeBetween($startDate, '+6 months'),
        ];
    }
}
