<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Comment;
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

        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $users = User::factory()->count(4)->create();

        $users->push($testUser);


        foreach ($users as $user) {
            $projects = Project::factory()->for($user, 'owner')->count(rand(1, 2))->create();

            foreach ($projects as $project) {
                // 1. Membres
                $project->members()->attach($user, ['role' => 'owner']);

                $others = $users->where('id', '!=', $user->id)->random(rand(1, 2));

                foreach ($others as $other) {
                    $project->members()->attach($other, [
                        'role' => fake()->randomElement(['editor', 'viewer']),
                    ]);
                }

                // 2. Tags du projet
                $tags = Tag::factory()->for($project)->count(4)->create();

                // 3. Membres et attributs communs
                $members = $project->members()->get();

                $attributes = [
                    'user_id' => fn() => $members->random()->id,
                    'assigned_to' => fn() => fake()->boolean(70) ? $members->random()->id : null,
                ];

                // 4 et 5. Tâches
                $tasks = Task::factory()->for($project)->count(7)->create($attributes);
                $completedTasks = Task::factory()->for($project)->completed()->count(3)->create($attributes);

                $allTasks = $tasks->merge($completedTasks);

                // 6. Sous-tâches
                $parents = $allTasks->random(rand(2, 3));

                foreach ($parents as $parent) {
                    $subtasks = Task::factory()->for($project)->for($parent, 'parent')->count(rand(1, 3))->create($attributes);
                    $allTasks = $allTasks->merge($subtasks);
                }

                // 7. Tags sur toutes les tâches avec les sous-tâches
                foreach ($allTasks as $task) {
                    $task->tags()->attach($tags->random(rand(0, 2)));
                }

                // 8. Commentaires
                foreach ($allTasks as $task) {
                    Comment::factory()->for($task)->count(rand(0, 3))->create([
                        'user_id' => fn() => $members->random()->id,
                    ]);
                }
            }
        }
    }
}
