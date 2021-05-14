<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Dancing Base') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.js" integrity="sha512-pF+DNRwavWMukUv/LyzDyDMn8U2uvqYQdJN0Zvilr6DDo/56xPDZdDoyPDYZRSL4aOKO/FGKXTpzDyQJ8je8Qw==" crossorigin="anonymous"></script>--}}

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
{{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.bootstrap4.css" integrity="sha512-WJ1jnnij6g+LY1YfSmPDGxY0j2Cq/I6PPA7/s4QJ/5sRca5ypbHhFF+Nam0TGfvpacrw9F0OGeZa0ROdNAsaEQ==" crossorigin="anonymous" />--}}
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
