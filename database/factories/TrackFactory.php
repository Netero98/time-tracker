<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Track;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Track>
 */
class TrackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Project::PROP_NAME => fake()->company(),
        ];
    }
}
