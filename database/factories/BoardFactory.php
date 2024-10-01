<?php

namespace Database\Factories;
use App\Models\Board;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Board>
 */
class BoardFactory extends Factory
{
    protected $model = Board::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'board_name' => $this->faker->word,
            'status' => 'active',
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
