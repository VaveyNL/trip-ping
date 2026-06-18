<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;

class TripPolicy
{
    // Смотреть и менять задачи может любой участник поездки
    public function view(User $user, Trip $trip): bool
    {
        return $trip->owner_id === $user->id
            || $trip->participants()->where('user_id', $user->id)->exists();
    }

    // Редактировать/удалять саму поездку — только владелец
    public function update(User $user, Trip $trip): bool
    {
        return $trip->owner_id === $user->id;
    }

    public function delete(User $user, Trip $trip): bool
    {
        return $trip->owner_id === $user->id;
    }
}
