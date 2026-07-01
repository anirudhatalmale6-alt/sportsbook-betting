<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#14181e">
    <meta name="description" content="BetZone - Premium Sports Betting & Casino">

    <title>{{ config('app.name', 'BetZone') }} - Sports Betting & Casino</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎰</text></svg>">

    <!-- Preconnect to fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark-800 text-gray-200 antialiased">
    <div id="app"></div>

    <noscript>
        <div style="display:flex;align-items:center;justify-content:center;min-height:100vh;background:#111;color:#999;font-family:sans-serif;padding:2rem;text-align:center;">
            <div>
                <h1 style="color:#20c997;font-size:1.5rem;margin-bottom:0.5rem;">BetZone</h1>
                <p>JavaScript is required to run this application. Please enable JavaScript in your browser settings.</p>
            </div>
        </div>
    </noscript>
</body>
</html>
