<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryIds = Category::pluck('id')->toArray();

        return [
            'category_id' => fake()->randomElement($categoryIds),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->numberBetween(1, 3),
            'email' => fake()->email(),
            'tel' => '090'.rand(10000000, 99999999),
            'address' => fake()->address(),
            'building' => fake()->company(),
            'detail' => fake()->realText(100),
        ];
    }
}
