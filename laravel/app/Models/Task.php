<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'title',
        'is_done',
        'created_by',
    ];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    // Поездка, к которой относится задача
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    // Кто создал задачу
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

