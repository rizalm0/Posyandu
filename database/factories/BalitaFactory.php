<?php

namespace Database\Factories;

use App\Models\Balita;
use App\Models\Posyandu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Balita>
 */
class BalitaFactory extends Factory
{
    protected $model = Balita::class;

    public function definition(): array
    {
        $birth = fake()->dateTimeBetween('-4 years', '-3 months');

        return [
            'posyandu_id' => Posyandu::factory(),
            'registration_number' => 'PSD-'.now()->format('Y').'-'.fake()->unique()->numerify('####'),
            'full_name' => fake()->name(),
            'date_of_birth' => $birth,
            'gender' => fake()->randomElement(['L', 'P']),
            'parent_name' => fake()->name(),
            'phone' => fake()->numerify('08##########'),
            'address' => fake()->streetAddress(),
            'registered_at' => fake()->dateTimeBetween($birth, 'now'),
            'is_active' => true,
        ];
    }
}
