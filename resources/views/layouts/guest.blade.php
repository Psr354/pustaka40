<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pustaka40') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-shell">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" class="text-decoration-none">
                        <h1 class="auth-brand mb-0">Pustaka<span>40</span></h1>
                    </a>
                    <a href="{{ route('home') }}" class="auth-back-link">Kembali ke beranda</a>
                </div>

                <div class="card auth-card border-0">
                    <div class="card-body p-4 p-md-5">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
