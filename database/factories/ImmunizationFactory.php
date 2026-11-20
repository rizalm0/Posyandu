<?php

namespace Database\Factories;

use App\Models\Balita;
use App\Models\Immunization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Immunization>
 */
class ImmunizationFactory extends Factory
{
    protected $model = Immunization::class;

    public function definition(): array
    {
        return [
            'balita_id' => Balita::factory(),
            'name' => fake()->randomElement(['BCG', 'Polio 1', 'DPT-HB-Hib 1', 'Campak/MR']),
            'administered_at' => fake()->dateTimeBetween('-2 years', 'now'),
            'status' => 'recorded',
            'notes' => null,
            'recorded_by' => null,
        ];
    }
}
