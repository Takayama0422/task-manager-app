<?php

namespace Database\Factories;

use App\Models\Textbook;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TextbookFactory extends Factory
{
    protected $model = Textbook::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'major_id' => $this->faker->numberBetween(1, 13),
            'mid_sort' => $this->faker->numberBetween(1, 5),
            'chapter_no' => $this->faker->numberBetween(1, 10),
        ];
    }
}
