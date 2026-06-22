<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;

class TripController extends Controller
{
    // Список поездок, где я участник
    public function index()
    {
        $trips = Trip::whereHas('participants', fn ($q) => $q->where('user_id', auth()->id()))
            ->withCount([
                'tasks',
                'tasks as done_tasks_count' => fn ($q) => $q->where('is_done', true),
            ])
            ->latest()
            ->get();

        return view('trips.index', compact('trips'));
    }

    public function create()
    {
        return view('trips.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['owner_id'] = auth()->id();
        $trip = Trip::create($data);
        $trip->participants()->attach(auth()->id(), ['role' => 'owner']);

        return redirect()->route('trips.show', $trip)->with('status', 'Поездка создана');
    }

    public function show(Trip $trip)
    {
        $this->authorize('view', $trip);

        $trip->load(['tasks' => fn ($q) => $q->latest(), 'participants']);

        return view('trips.show', compact('trip'));
    }

    public function edit(Trip $trip)
    {
        $this->authorize('update', $trip);

        return view('trips.edit', compact('trip'));
    }

    public function update(Request $request, Trip $trip)
    {
        $this->authorize('update', $trip);

        $trip->update($this->validated($request));

        return redirect()->route('trips.show', $trip)->with('status', 'Поездка обновлена');
    }

    public function destroy(Trip $trip)
    {
        $this->authorize('delete', $trip);

        $trip->delete();

        return redirect()->route('trips.index')->with('status', 'Поездка удалена');
    }

    // Добавить участника по email (только владелец)
    public function addParticipant(Request $request, Trip $trip)
    {
        $this->authorize('update', $trip);

        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return back()->with('status', 'Пользователь с таким email не найден');
        }

        $trip->participants()->syncWithoutDetaching([
            $user->id => ['role' => 'member'],
        ]);

        return back()->with('status', 'Участник добавлен: ' . $user->name);
    }

    // Общая валидация для store/update
    private function validated(Request $request): array
    {
        return $request->validate([
            'name'        => 'required|string|max:255',
            'destination' => 'nullable|string|max:255',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);
    }
}
