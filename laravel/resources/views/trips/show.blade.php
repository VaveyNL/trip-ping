<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $trip->name }}</h4>
            @can('update', $trip)
                <div class="d-flex gap-2 align-items-center">
                    <a href="{{ route('trips.edit', $trip) }}" class="btn btn-outline-secondary btn-sm">Изменить</a>
                    <form method="POST" action="{{ route('trips.destroy', $trip) }}" onsubmit="return confirm('Удалить поездку?')" class="m-0">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm">Удалить</button>
                    </form>
                </div>
            @endcan
        </div>
    </x-slot>

    <div class="container" style="max-width: 720px;">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="text-muted small">
                    {{ $trip->destination }}
                    @if ($trip->start_date)
                        · {{ $trip->start_date->format('d.m.Y') }}–{{ $trip->end_date?->format('d.m.Y') }}
                    @endif
                </div>
                @if ($trip->description)
                    <p class="mt-2 mb-0">{{ $trip->description }}</p>
                @endif
                <div class="mt-2 small text-secondary">Участники: {{ $trip->participants->pluck('name')->join(', ') }}</div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Чек-лист сборов</h5>

                <ul class="list-group list-group-flush mb-3">
                    @foreach ($trip->tasks as $task)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="m-0 d-flex align-items-center gap-2">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-link p-0 fs-5 text-decoration-none">{{ $task->is_done ? '☑' : '☐' }}</button>
                                <span class="{{ $task->is_done ? 'text-decoration-line-through text-muted' : '' }}">{{ $task->title }}</span>
                            </form>
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="m-0">
                                @csrf @method('DELETE')
                                <button class="btn btn-link btn-sm text-muted p-0">✕</button>
                            </form>
                        </li>
                    @endforeach
                </ul>

                <form method="POST" action="{{ route('tasks.store', $trip) }}" class="d-flex gap-2">
                    @csrf
                    <input name="title" placeholder="Новая задача…" required class="form-control">
                    <button class="btn btn-primary">Добавить</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
