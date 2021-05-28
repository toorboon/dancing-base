@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="bg-dark pt-2 col-12">
            <p>This page contains videos uploaded by these users:
                @if(count($creators)>0)
                    <ul>
                        @foreach($creators as $creator)
                                <li>{{ $creator }}</li>
                        @endforeach
                    </ul>
                @endif
            </p>
            <p>The aforementioned administrators make sure that he or she has all rights
                regarding the videos which got uploaded.</p>
            <p>If this is not the case, please inform the Administrator of this page
                about this violation and he or she will remove the problematic video.</p>
            <br>
            <p>
            The hoster or developer of this page cannot made anyhow responsible for any violation of European law due to
                copyright infringements. </p>
        </div>
    </div>
    <br>
    <div class="row text-center">
        <div class="col-12 col-md-5 offset-md-8 mx-auto pt-3 bg-dark">
            <h5>Responsible admin for this site</h5>
            <p>Name: {{ ucfirst($responsibleAdmin->name) }}</p>
            <p>E-mail: {{ $responsibleAdmin->email }}</p>
        </div>
    </div>

@endsection
