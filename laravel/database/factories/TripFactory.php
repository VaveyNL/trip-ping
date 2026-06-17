<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+1 week', '+2 months');
        $end   = (clone $start)->modify('+' . rand(3, 10) . ' days');

        return [
            'name'        => $this->faker->city() . ' ' . $this->faker->randomElement(['тур', 'поездка', 'выезд']),
            'description' => $this->faker->sentence(),
            'destination' => $this->faker->city(),
            'start_date'  => $start,
            'end_date'    => $end,
            'owner_id'    => User::factory(),
        ];
    }
}
