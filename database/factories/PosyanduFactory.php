<?php

namespace Database\Factories;

use App\Models\Posyandu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Posyandu>
 */
class PosyanduFactory extends Factory
{
    protected $model = Posyandu::class;

    public function definition(): array
    {
        return [
            'name' => 'Posyandu '.fake()->unique()->words(2, true),
            'code' => 'PSD-'.fake()->unique()->bothify('??##'),
            'address' => fake()->address(),
            'phone' => fake()->numerify('08##########'),
            'alert_no_exam_days' => 60,
        ];
    }
}
