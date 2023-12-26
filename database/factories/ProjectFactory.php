<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $user = User::query()->where(User::PROP_EMAIL, DatabaseSeeder::TEST_USER_EMAIL)->firstOrFail();

        return [
            Project::PROP_USER_ID => $user->id,
            Project::PROP_NAME => fake()->company(),
        ];
    }
}
