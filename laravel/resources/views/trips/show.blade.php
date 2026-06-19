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
                <div id="checklist-root">
                    <p class="text-muted">Загрузка чек-листа…</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.TRIP_ID = {{ $trip->id }};
            window.CSRF_TOKEN = "{{ csrf_token() }}";
            window.INITIAL_TASKS = {!! $trip->tasks->map(fn ($t) => ['id' => $t->id, 'title' => $t->title, 'is_done' => (bool) $t->is_done])->toJson() !!};
        </script>
        <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
        <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
        @verbatim
        <script>
            const { useState, useEffect } = React;
            const e = React.createElement;

            function Checklist() {
                const tripId = window.TRIP_ID;
                const csrf = window.CSRF_TOKEN;
                const [tasks, setTasks] = useState(window.INITIAL_TASKS || []);
                const [title, setTitle] = useState('');
                const [online, setOnline] = useState(false);

                useEffect(function () {
                    const proto = location.protocol === 'https:' ? 'wss' : 'ws';
                    const sock = new WebSocket(proto + '://' + location.host + '/ws');
                    sock.onopen = function () { setOnline(true); };
                    sock.onclose = function () { setOnline(false); };
                    sock.onmessage = function (ev) {
                        const msg = JSON.parse(ev.data);
                        if (msg.trip_id !== tripId) return;
                        if (msg.event === 'task.added') {
                            setTasks(function (prev) {
                                if (prev.some(function (t) { return t.id === msg.task.id; })) return prev;
                                return prev.concat([msg.task]);
                            });
                        } else if (msg.event === 'task.toggled') {
                            setTasks(function (prev) {
                                return prev.map(function (t) {
                                    return t.id === msg.task.id ? Object.assign({}, t, { is_done: msg.task.is_done }) : t;
                                });
                            });
                        } else if (msg.event === 'task.deleted') {
                            setTasks(function (prev) {
                                return prev.filter(function (t) { return t.id !== msg.task_id; });
                            });
                        }
                    };
                    return function () { sock.close(); };
                }, []);

                const headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                };

                function addTask(ev) {
                    ev.preventDefault();
                    if (!title.trim()) return;
                    fetch('/trips/' + tripId + '/tasks', {
                        method: 'POST', headers: headers, credentials: 'same-origin',
                        body: JSON.stringify({ title: title }),
                    });
                    setTitle('');
                }

                function toggleTask(id) {
                    fetch('/tasks/' + id + '/toggle', { method: 'PATCH', headers: headers, credentials: 'same-origin' });
                }

                function deleteTask(id) {
                    fetch('/tasks/' + id, { method: 'DELETE', headers: headers, credentials: 'same-origin' });
                }

                const items = tasks.map(function (t) {
                    return e('li', { key: t.id, className: 'list-group-item d-flex justify-content-between align-items-center px-0' },
                        e('label', { className: 'd-flex align-items-center gap-2 m-0', style: { cursor: 'pointer' } },
                            e('input', { type: 'checkbox', checked: !!t.is_done, onChange: function () { toggleTask(t.id); } }),
                            e('span', { className: t.is_done ? 'text-decoration-line-through text-muted' : '' }, t.title)
                        ),
                        e('button', { className: 'btn btn-link btn-sm text-muted p-0', onClick: function () { deleteTask(t.id); } }, '✕')
                    );
                });

                return e('div', null,
                    e('div', { className: 'd-flex align-items-center gap-2 mb-3' },
                        e('h5', { className: 'mb-0' }, 'Чек-лист сборов'),
                        e('span', { className: 'badge ' + (online ? 'bg-success' : 'bg-secondary') }, online ? 'онлайн' : 'оффлайн')
                    ),
                    e('ul', { className: 'list-group list-group-flush mb-3' }, items),
                    e('form', { onSubmit: addTask, className: 'd-flex gap-2' },
                        e('input', { className: 'form-control', placeholder: 'Новая задача…', value: title, onChange: function (ev) { setTitle(ev.target.value); } }),
                        e('button', { className: 'btn btn-primary', type: 'submit' }, 'Добавить')
                    )
                );
            }

            ReactDOM.createRoot(document.getElementById('checklist-root')).render(e(Checklist));
        </script>
        @endverbatim
    @endpush
</x-app-layout>
