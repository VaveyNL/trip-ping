<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'trip_id'    => Trip::factory(),
            'title'      => $this->faker->randomElement([
                'Купить билеты',
                'Забронировать отель',
                'Собрать аптечку',
                'Зарядки и павербанк',
                'Документы и страховка',
                'Обменять валюту',
            ]),
            'is_done'    => $this->faker->boolean(30),
            'created_by' => User::factory(),
        ];
    }
}
