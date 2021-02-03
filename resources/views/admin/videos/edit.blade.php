@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Video</div>

                    <div class="card-body">
                        <form method="POST" action="{{ action('Admin\VideoController@update', $video) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group row">
                                <label for="title" class="col-md-4 col-form-label text-md-right">Title</label>

                                <div class="col-md-6">
                                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') ?? $video->title }}" required autocomplete="title" autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="description" class="col-md-4 col-form-label text-md-right">Description</label>

                                <div class="col-md-6">
                                    <input id="description" type="text" class="form-control" name="description" value="{{ old('description') ?? $video->description }}" autocomplete="description">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="tag" class="col-md-4 col-form-label text-md-right">Tag</label>

                                <div class="col-md-6">
                                    <input id="tag" type="text" class="form-control" name="tag" value="{{ old('tag') ?? $video->tag }}" autocomplete="tag">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="category" class="col-md-4 col-form-label text-md-right">Category</label>

                                <div class="col-md-6">
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

                            <div class="form-group row">
                                <label for="video" class="col-md-4 col-form-label text-md-right">Upload video</label>

                                <div class="col-md-6">
                                    <input id="video" name="video" type="file">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Edit Video') }}
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection