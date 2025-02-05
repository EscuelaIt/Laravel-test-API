<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company,
            'email' => $this->faker->unique()->safeEmail,
            'telephone' => $this->faker->phoneNumber,
        ];
    }

    public function standard($userId) {
        return $this->state([
            'name' => "Estandar Company",
            'user_id' => $userId,
        ])->has(Project::factory()->count(3));
    }

    public function premium() {
        return $this->state([
            'name' => 'Premium',
        ]);
    }

    public function withCurrentProjectsAndIntervals($projectCount) {
        return $this->has(Project::factory()->count($projectCount));
    }
}
