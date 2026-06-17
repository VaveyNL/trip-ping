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
        $friend = User::firstOrCreate(
            ['email' => 'friend@trip.local'],
            ['name' => 'Аня', 'password' => 'password']
        );

        // Две поездки во владении owner.
        $trips = Trip::factory()->count(2)->create(['owner_id' => $owner->id]);

        foreach ($trips as $trip) {
            // Участники: владелец + друг.
            $trip->participants()->syncWithoutDetaching([
                $owner->id  => ['role' => 'owner'],
                $friend->id => ['role' => 'member'],
            ]);

            // По 5 задач на поездку.
            Task::factory()->count(5)->create([
                'trip_id'    => $trip->id,
                'created_by' => $owner->id,
            ]);
        }
    }
}
