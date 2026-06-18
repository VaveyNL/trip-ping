<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Trip;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request, Trip $trip)
    {
        $this->authorize('view', $trip); // участник поездки может добавлять

        $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $trip->tasks()->create([
            'title'      => $data['title'],
            'created_by' => auth()->id(),
        ]);

        return back();
    }

    public function toggle(Task $task)
    {
        $this->authorize('view', $task->trip);

        $task->update(['is_done' => ! $task->is_done]);

        return back();
    }

    public function destroy(Task $task)
    {
        $this->authorize('view', $task->trip);

        $task->delete();

        return back();
    }
}
