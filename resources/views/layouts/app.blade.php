<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dancing Base') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body class="bootstrap-dark">
    <div id="app" class="">

        <!-- Get the Navbar -->
        @include('inc.navbar')

        <!-- Put the Message Box -->
        <div class="message_box row d-flex-column justify-content-center mt-3 col-10 offset-1 w-100 text-center">
            @include('inc.messages')
        </div>

        <!-- Get the main App -->
        <main class=" container-lg text-white ">
            @yield('content')
        </main>

    </div>
</body>
</html>
