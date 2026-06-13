<?php

namespace Database\Factories;

use App\Models\ProgressLog;
use App\Models\Textbook;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgressLogFactory extends Factory
{
    protected $model = ProgressLog::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'textbook_id' => Textbook::factory(),
            'status'      => $this->faker->randomElement([0, 1, 2]),
            'is_flagged'  => 0,
            'memo'        => $this->faker->optional()->sentence(),
        ];
    }

    public function flagged(): static
    {
        return $this->state(['is_flagged' => true]);
    }

    public function completed(): static
    {
        return $this->state(['status' => 2]);
    }
}