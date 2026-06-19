<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TaskController extends Controller
{
    public function store(Request $request, Trip $trip)
    {
        $this->authorize('view', $trip);

        $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $task = $trip->tasks()->create([
            'title'      => $data['title'],
            'created_by' => auth()->id(),
        ]);

        Redis::publish('task.added', json_encode([
            'trip_id' => $trip->id,
            'task'    => [
                'id'      => $task->id,
                'title'   => $task->title,
                'is_done' => $task->is_done,
            ],
        ]));

        return back();
    }

    public function toggle(Task $task)
    {
        $this->authorize('view', $task->trip);

        $task->update(['is_done' => ! $task->is_done]);

        Redis::publish('task.toggled', json_encode([
            'trip_id' => $task->trip_id,
            'task'    => [
                'id'      => $task->id,
                'is_done' => $task->is_done,
            ],
        ]));

        return back();
    }

    public function destroy(Task $task)
    {
        $this->authorize('view', $task->trip);

        $tripId = $task->trip_id;
        $taskId = $task->id;
        $task->delete();

        Redis::publish('task.deleted', json_encode([
            'trip_id' => $tripId,
            'task_id' => $taskId,
        ]));

        return back();
    }
}
