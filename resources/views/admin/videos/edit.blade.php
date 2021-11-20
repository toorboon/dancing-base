@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header">Edit Video</div>

                    <div class="card-body">
                        <form method="POST" action="{{ action('Admin\VideoController@update', $video) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label for="title" class="col-md-2 col-form-label text-md-right">Title</label>

                                <div class="col-md-10">
                                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') ?? $video->title }}" required autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="tts" class="col-md-2 col-form-label text-md-right" title="Check this, if you want Google to create a training .mp3 from your title field!">TTS?</label>

                                <div class="col ">
                                    <input id="tts" type="checkbox" class="my-auto h-100" name="tts" >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="description" class="col-md-2 col-form-label text-md-right">Description</label>

                                <div class="col-md-10">
                                    <textarea id="description" class="form-control ckeditor" name="description">{{ old('description') ?? $video->description }}</textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="tag" class="col-md-2 col-form-label text-md-right">Tag</label>

                                <div class="col-md-4">
                                    <select multiple="multiple" id="tags" class="form-control select2" name="tags[]" title="Please choose/create/delete Tags">
                                        @foreach($video->tags as $tag)
                                            <option value="{{ $tag->normalized }}" selected>{{ $tag->name }}</option>
                                        @endforeach
                                        @foreach($tagsOption as $tag)
                                            <option value="{{ $tag->normalized }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="category" class="col-md-2 col-form-label text-md-right">Category</label>

                                <div class="col-md-4">
                                    <select id="category" class="form-control custom-select" name="category" required title="Please choose category">
                                        <option value="Please choose" disabled selected>Please choose</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @if(old('category') AND old('category') === $category->title) selected
                                                @else {{ $category->id === $video->category_id ? 'selected' : '' }}
                                                @endif
                                            >{{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row ">
                                <label for="video" class="col-md-2 col-form-label text-md-right">Upload video</label>

                                <div class="col">
                                    <input id="video" name="video" type="file">
                                </div>
                                <div class="mx-auto mt-2 mr-md-3">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Edit Video') }}
                                    </button>
                                    <a href="{{ route('admin.videos.show', $video->id) }}" class="btn btn-secondary">Back</a>
                                </div>
                            </div>

                        </form>

                    </div>
                    {{--Video preview--}}
                    <div class="embed-responsive embed-responsive-16by9">
                        @if($video->video)
                            <video id="video_{{ $video->id }}" class="embed-responsive-item" controls muted autoplay><source src="/storage/videos/{{ $video->video }}" type="video/mp4">
                                Your browser does not support the video tag!
                            </video>
                        @else
                            <img class="embed-responsive-item" src="{{ asset('images/novideo.jpg') }}">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
