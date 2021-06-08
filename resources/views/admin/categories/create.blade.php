@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card">
                    <div class="card-header">{{__('Create Category')}}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.categories.store') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="title" class="col-md-2 col-form-label text-md-right">Title</label>

                                <div class="col-md-10">
                                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') ?? 'Salsa' }}" required autocomplete="title" autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="description" class="col-md-2 col-form-label text-md-right">Description</label>

                                <div class="col-md-10">
                                    <textarea id="description" class="form-control ckeditor" name="description" value="{{ old('description') }}"></textarea>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-lg-6 offset-lg-4 offset-md-4">
                                    <button type="submit" class="btn btn-primary">{{ __('Create Category') }}</button>
                                    <a href="{{ route('admin.dashboard.index') }}" class="btn btn-secondary">Back</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
