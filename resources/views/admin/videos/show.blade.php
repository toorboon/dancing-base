@extends('layouts.app')

@section('content')
    <div class="card bg-transparent border-0">
        <div class="embed-responsive embed-responsive-16by9">
            <video id="video_{{ $video->id }}" class="embed-responsive-item" controls muted autoplay><source src="/storage/videos/{{ $video->video }}" type="video/mp4">
                Your browser does not support the video tag!
            </video>
        </div>
        <div id="" class="actions d-flex flex-column justify-content-between">
            <button type="button" class="btn btn-sm btn-dark text-right">...</button>
            <div id="" class="buttons d-none flex-column align-items-stretch">
                <a href="{{ route('admin.videos.index') }}" class="btn btn-sm btn-secondary mt-1">Back</a>

                <a href="{{ route('admin.videos.edit', $video->id) }}" class="btn btn-sm btn-secondary mt-1 w-100">Edit</a>
                <form action="{{ action('Admin\VideoController@destroy', $video) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-secondary mt-1 w-100">Delete</button>
                </form>
            </div>
        </div>

        <table id="information_box" class="table table-sm table-dark table-bordered">
            <tbody>
                <tr>
                    <th scope="row">Title:</th>
                    <td>{{ $video->title }}</td>
                </tr>
                <tr>
                    <th scope="row">Desc:</th>
                    <td id="description_box">{{ $video->description }}</td>
                </tr>
                <tr>
                    <th scope="row">Tags:</th>
                    <td>{{ $video->taglist }}</td>
                </tr>
                <tr>
                    <th scope="row">Cat:</th>
                    <td>{{ $video->category->title }}</td>
                </tr>
                <tr>
                    <th scope="row">Progress:</th>
                    <td class="font-weight-bold">
                        <span id="rated_index_{{ $video->id }}" class="d-none rated_index" data-index="{{ $rated_index }}"></span>
                        @for($i=0; $i<5; $i++)
                            <span class="voting_stars text-secondary" data-index="{{ $i+1 }}">&#10022;</span>
                        @endfor
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
