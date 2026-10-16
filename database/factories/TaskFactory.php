<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => rtrim(fake()->sentence(4), '.'),
            'description' => fake()->optional()->paragraph(),
            'priority' => fake()->numberBetween(1, 3),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'status' => fake()->randomElement(['todo', 'in_progress']),
            'user_id' => User::factory(),
            'project_id' => Project::factory(),
            'assigned_to' => null,
            'parent_id' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn() => [
            'completed_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'status' => 'done',
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn() => [
            'priority' => 3,
        ]);
    }
}
