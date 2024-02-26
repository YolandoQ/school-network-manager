<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            $school = School::create([
                'name' => $faker->company,
            ]);

            for ($j = 0; $j < 10; $j++) {
                $group = $school->groups()->create([
                    'name' => $faker->word,
                ]);

                for ($k = 0; $k < 10; $k++) {
                    $group->students()->create([
                        'name' => $faker->name,
                        'email' => $faker->unique()->safeEmail,
                    ]);
                }
            }
        }
    }
}
