<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    public function run(): void
    {
        // Тестовые пользователи (пароль захешируется автоматически).
        $owner = User::firstOrCreate(
            ['email' => 'owner@trip.local'],
            ['name' => 'Владимир', 'password' => 'password']
        );
        $alina = User::firstOrCreate(
            ['email' => 'alina@trip.local'],
            ['name' => 'Алина', 'password' => 'password']
        );
        $alexey = User::firstOrCreate(
            ['email' => 'alexey@trip.local'],
            ['name' => 'Алексей', 'password' => 'password']
        );

        // Поездки во владении owner (поменяй число, если хочешь больше/меньше).
        $trips = Trip::factory()->count(4)->create(['owner_id' => $owner->id]);

        foreach ($trips as $trip) {
            // Участники: владелец + Алина + Алексей.
            $trip->participants()->syncWithoutDetaching([
                $owner->id  => ['role' => 'owner'],
                $alina->id  => ['role' => 'member'],
                $alexey->id => ['role' => 'member'],
            ]);

            // По 5 задач на поездку.
            Task::factory()->count(5)->create([
                'trip_id'    => $trip->id,
                'created_by' => $owner->id,
            ]);
        }
    }
}
