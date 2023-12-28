<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Track;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public const TEST_USER_EMAIL = 'test@example.com';

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

         \App\Models\User::factory()->create([
             'name' => 'Test User',
             'email' => self::TEST_USER_EMAIL, //password
         ]);

         Project::factory(2)->create([
             Project::PROP_USER_ID => 1,
         ]);

         Track::factory(3)->create([
             Track::PROP_PROJECT_ID => 1,
         ]);

        Track::factory(2)->create([
            Track::PROP_PROJECT_ID => 2,
        ]);
    }
}
