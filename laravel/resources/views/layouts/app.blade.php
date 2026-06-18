<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TripPing')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('trips.index') }}">TripPing</a>
            <div class="d-flex align-items-center gap-3">
                @auth
                    <span class="text-light">Привет, {{ auth()->user()->name }}!</span>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button class="btn btn-outline-light btn-sm">Выйти</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    @isset($header)
        <header class="container mb-4" style="max-width: 720px;">
            {{ $header }}
        </header>
    @endisset

    <main class="pb-5">
        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>
