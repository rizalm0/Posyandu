<?php

namespace Database\Factories;

use App\Models\Balita;
use App\Models\Examination;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Examination>
 */
class ExaminationFactory extends Factory
{
    protected $model = Examination::class;

    public function definition(): array
    {
        return [
            'balita_id' => Balita::factory(),
            'posyandu_id' => fn (array $attributes) => Balita::find($attributes['balita_id'])->posyandu_id,
            'examined_at' => fake()->dateTimeBetween('-8 months', 'now'),
            'weight_kg' => fake()->randomFloat(2, 4, 18),
            'height_cm' => fake()->randomFloat(1, 55, 110),
            'head_circumference_cm' => fake()->randomFloat(1, 34, 51),
            'notes' => null,
            'recorded_by' => null,
        ];
    }
}
