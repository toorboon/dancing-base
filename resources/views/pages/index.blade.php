@extends('layouts.app')

@section('content')

    <div id="jumbotron" class="jumbotron bg-secondary text-center">
        <h1>Welcome to the Dancing Base!</h1>
        @guest
            <p>You want to save all your recorded dancing videos and categorise them?</p>
            <p>You want to make sure that you have all your dancing available on any device?</p>
            <p>Please log in to proceed into the world of fantastic dancing!<p>
        @endguest
    </div>

@endsection
