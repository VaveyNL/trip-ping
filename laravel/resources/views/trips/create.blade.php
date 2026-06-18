<x-app-layout>
    <x-slot name="header">
        <h4 class="mb-0">Новая поездка</h4>
    </x-slot>

    <div class="container" style="max-width: 640px;">
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('trips.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Название</label>
                        <input name="name" value="{{ old('name') }}" required class="form-control @error('name') is-invalid @enderror">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Направление</label>
                        <input name="destination" value="{{ old('destination') }}" class="form-control">
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Дата начала</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control">
                        </div>
                        <div class="col">
                            <label class="form-label">Дата конца</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Создать</button>
                    <a href="{{ route('trips.index') }}" class="btn btn-link">Отмена</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
