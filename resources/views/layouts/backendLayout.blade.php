<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- CSRF Token Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') – IT Asset Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
    @vite(['resources/js/app.js'])
</head>

<body>
    @include('includes.header')

    @yield('content')

    <script src="{{ asset('assets/js/app.js') }}"></script>
    @yield('scripts')
</body>

</html>
