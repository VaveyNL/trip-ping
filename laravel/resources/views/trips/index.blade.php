<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Мои поездки</h4>
            <a href="{{ route('trips.create') }}" class="btn btn-primary btn-sm">+ Новая поездка</a>
        </div>
    </x-slot>

    <div class="container" style="max-width: 720px;">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @forelse ($trips as $trip)
            <a href="{{ route('trips.show', $trip) }}" class="text-decoration-none">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-1 text-dark">{{ $trip->name }}</h5>
                        <div class="text-muted small">
                            {{ $trip->destination }}
                            @if ($trip->start_date)
                                · {{ $trip->start_date->format('d.m') }}–{{ $trip->end_date?->format('d.m') }}
                            @endif
                        </div>
                        <div class="text-secondary small mt-2">{{ $trip->done_tasks_count }} из {{ $trip->tasks_count }} готово</div>
                    </div>
                </div>
            </a>
        @empty
            <p class="text-muted">Поездок пока нет. Создайте первую!</p>
        @endforelse
    </div>
</x-app-layout>
