<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pustaka40') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a href="#konten-utama" class="skip-link">Lewati ke konten utama</a>
    <div class="app-shell">
        @include('layouts.navigation')

        <main class="app-content" id="konten-utama" tabindex="-1">
            <div class="container py-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @isset($header)
                    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
                        <h1 class="h4 mb-0">{{ $header }}</h1>
                    </div>
                @endisset

                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
