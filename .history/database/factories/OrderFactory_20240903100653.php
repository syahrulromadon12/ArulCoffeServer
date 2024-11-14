<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first(); // Mengambil user_id yang valid

        return [
            'user_id' => User::factory(),
            'status_id' => Status::inRandomOrder()->first()->id ?? Status::factory(), // Ambil status yang ada atau buat baru
            'total_price' => $this->faker->randomFloat(2, 20, 500),
        ];
    }
}
