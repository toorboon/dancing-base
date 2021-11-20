@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header">Upload Video</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.videos.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group row">
                                <label for="title" class="col-md-2 col-form-label text-md-right">Title</label>

                                <div class="col-md-10">
                                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') ?? 'Krasses Video 1' }}" required autofocus>
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
                                    <textarea id="description" class="form-control ckeditor" name="description"></textarea>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="tags" class="col-md-2 col-form-label text-md-right">Tags</label>

                                <div class="col-md-4">
                                    <select multiple="multiple" id="tags" class="form-control select2" name="tags[]" title="Please choose/create Tags">
                                        @foreach($tags as $tag)
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
                                            <option value="{{ $category->id }}" {{ $category->title === old('category') ? 'selected' : '' }}>{{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="video" class="col-md-2 col-form-label text-md-right">Upload video</label>

                                <div class="col-md-10">
                                    <input id="video" name="video" type="file">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Upload Video') }}
                                    </button>
                                    <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">Back</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
