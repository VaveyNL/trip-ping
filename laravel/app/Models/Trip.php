<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'destination',
        'start_date',
        'end_date',
        'owner_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    // Владелец поездки
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Задачи чек-листа
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Участники (many-to-many через trip_participants)
    public function participants()
    {
        return $this->belongsToMany(User::class, 'trip_participants')
                    ->withPivot('role')
                    ->withTimestamps();
    }
}

