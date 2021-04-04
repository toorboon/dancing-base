@extends('layouts.app')

@section('content')

    {{--Select for category--}}
    <div class="row">
        <div class="col-12">
            <div class="input-group d-flex justify-content-center">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="category">Choose your category</label>
                </div>

                <form action="{{ route('admin.videos.index') }}" method="get">
                    <select id="categoryindex" class="form-control custom-select" name="category" required title="Please choose category">
                        <option value="Please choose" disabled @if(!(old('category')) AND !$oldCategory) selected @endif>Please choose</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                @if($oldCategory) {{ $oldCategory == $category->id ? 'selected' : '' }}
                                @else {{ old('category') == $category->id ? 'selected' : '' }}
                                @endif>{{ $category->title }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>

    {{--Actions navigation--}}
    <div class="row mt-4">
        <ul class="navbar-nav mx-auto mb-3">
            <li class="nav-item dropdown text-center">
                <a id="actionDropdown" class="btn btn-dark dropdown-toggle border-secondary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" v-pre>
                    Actions <span class="caret"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="actionDropdown">
                    <a class="dropdown-item" href="{{ route('admin.videos.create') }}">Upload Video</a>
                </div>
            </li>
        </ul>
    </div>

    @if(count($videos)>0)

        <div class="row row-cols-1 row-cols-md-3  mt-5">
            @foreach($videos as $video)

            <div class="col mb-4 video" data-href="{{ route('admin.videos.show', $video->id) }}">
                <div class="card h-100 border-secondary">
                    <div class="embed-responsive embed-responsive-4by3">
                        {{--option menu for each video--}}
                        <div class="actions d-flex flex-column justify-content-between p-3">
                            <button type="button" class="btn btn-sm btn-dark">...</button>
                            <div id="" class="buttons d-none flex-column align-items-stretch">
                                <a href="{{ route('admin.videos.edit', $video->id) }}" class="btn btn-sm btn-secondary mt-1 w-100">Edit</a>
                                <form action="{{ action('Admin\VideoController@destroy', $video) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary mt-1 w-100">Delete</button>
                                </form>
                            </div>
                        </div>
                        <video muted class="card-img-top embed-responsive-item"><source src="/storage/videos/{{ $video->timelapse }}" type="video/webm" >
                            Your browser does not support the video tag!
                        </video>
                    </div>
                    <h5 class="card-header">{{ $video->title }}</h5>
                    <div class="card-body">
                        <small class="card-text">{{ $video->description }}</small>
                    </div>
                    <div class="card-footer text-muted text-center p-0 m-0">
                        <small>created {{ $video->created_at->diffInDays() !== 0 ? $video->created_at->diffInDays()." day(s) ago" : 'today' }} by {{ $video->user->name }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    @else
        <div class="row">
        <h5 class="bg-dark text-center mt-5 mx-auto p-2">Nothing to display, please upload videos!</h5>
        </div>
    @endif

@endsection
