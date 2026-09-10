<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Smart Vadodara Connect')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">

    <div class="citizen-shell mx-auto min-h-screen max-w-md bg-white shadow-xl">

        @yield('header')

        @yield('content')

    </div>

    @stack('scripts')

</body>
</html>