@extends('layouts.app')

@section('content')
    <div class="embed-responsive embed-responsive-16by9">
        <video id="video_{{ $video->id }}" class="embed-responsive-item" controls muted autoplay><source src="/storage/videos/{{ $video->video }}" type="video/mp4">
            Your browser does not support the video tag!
        </video>
    </div>
    <div id="" class="actions d-flex flex-column justify-content-between">
        <button type="button" class="btn btn-sm btn-dark text-right">...</button>
        <div id="" class="buttons d-none flex-column align-items-stretch">
            <a href="{{ route('admin.videos.index', $video->category) }}" class="btn btn-sm btn-secondary mt-1">Back</a>

            <a href="{{ route('admin.videos.edit', $video->id) }}" class="btn btn-sm btn-secondary mt-1 w-100">Edit</a>
            <form action="{{ action('Admin\VideoController@destroy', $video) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-secondary mt-1 w-100">Delete</button>
            </form>
        </div>
    </div>
    <div id="information" class="bg-dark p-2">
        <h6>Title: {{ $video->title }}</h6>
        <h6>Desc: {{ $video->description }}</h6>
        <h6>Tag: {{ $video->tag }}</h6>
        <h6>Cat: {{ $video->category->title }}</h6>
    </div>
@endsection
