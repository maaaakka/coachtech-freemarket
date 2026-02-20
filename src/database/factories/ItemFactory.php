<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Item;
use App\Models\Like;
use App\Models\Purchase; // ← これ追加
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'brand' => $this->faker->company(),
            'price' => 1000,
            'description' => $this->faker->sentence,
            'condition' => 1,
            'image_path' => 'dummy.jpg',
            'user_id' => User::factory(), // 仮（後で改善可）
        ];
    }
}
