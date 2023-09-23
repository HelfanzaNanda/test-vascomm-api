<?php

namespace Database\Factories;

use App\Enums\StatusEnum;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $img = File::all()->random(1)->first();
        return [
            'name' => fake()->sentence(2),
            'file_id' => $img->id,
            // 'price' => fake()->randomFloat(2, 10, 1000),
            'price' => fake()->numberBetween($min = 1000, $max = 100000),
            'status' => fake()->randomElement([StatusEnum::ACTIVE->value, StatusEnum::NOT_ACTIVE->value])
        ];
    }
}
