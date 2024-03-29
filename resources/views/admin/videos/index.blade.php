@extends('layouts.app')

@section('content')
    {{--Select for category--}}
    <div class="row">
        <button id="toolboxtoggler" type="button" class="btn btn-block btn-secondary mx-2" data-toogle="collapse" data-target="#toolbox">Toolbox</button>

        {{--Toolbox items--}}
        <div id="toolbox" class="col-12 collapse mt-2 ">
            <form action="{{ route('admin.videos.index') }}" method="get">
                <div class="d-flex flex-column">
                    <div class="w-100 mb-3 ">
                        <div class="d-flex flex-sm-nowrap flex-wrap justify-content-center">
                            <div class="d-flex w-100">
                                <input id="search" class="form-control text-center" type="text" name="search" placeholder="Full Text Search" title="This field does a full text search for Guests in first name, last name, document, address, gender and notes!" value="{{ old('search') }}">
                                <button id="clear_search" type="button" class="btn"><strong>&#10539;</strong></button>
                            </div>
                            <div class="d-flex mt-2 mt-sm-0">
                                <button id="reset_video_search" type="button" class="btn btn-dark btn-sm">Reset</button>
                                <button id="filter" type="submit" class="btn btn-dark btn-sm ml-1">Filter</button>
                            </div>
                        </div>
                    </div>

                    {{--Single item search and action menu--}}
                    <div class="d-flex flex-column flex-md-row justify-content-between">
                        <div class="input-group ">
                            <div class="d-none d-md-inline input-group-prepend">
                                <label class="input-group-text" for="category">Search category</label>
                            </div>
                            <select id="categoryindex" class="form-control custom-select mb-2 mb-md-0" name="category" title="Please choose category to be searched">
                                <option value='all' @if(!($selectedCategory)) selected @endif>All</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group">
                            <div class="d-none d-md-inline input-group-prepend">
                                <label class="input-group-text" for="progress_index">Search progress</label>
                            </div>
                            <select id="progress_index" class="form-control custom-select flex-shrink-1 mb-2 mb-md-0" name="progress_index" title="Please choose the video progress you are interested in!">
                                <option value='all' @if(!($selectedProgress)) selected @endif>All</option>
                                @for($i=0; $i<5; $i++)
                                    <option value="{{ $i+1 }}" {{ $selectedProgress == $i+1 ? 'selected' : '' }}>{{ $i+1 }} PStar</option>
                                @endfor
                            </select>
                        </div>
                        {{--Actions navigation--}}
                        <div class="col-md-4 col-lg-2 p-0 flex-shrink-1">
                            <ul class="navbar-nav mx-auto">
                                <li class="nav-item dropdown text-center">
                                    <a id="actionDropdown" class="btn btn-dark btn-block dropdown-toggle border-secondary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" v-pre>
                                        Actions <span class="caret"></span>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="actionDropdown">
                                        @can('manage-app')
                                            <a class="dropdown-item" href="{{ route('admin.videos.create') }}">Upload Video</a>
                                        @endcan
                                        <a id="showTrainer" class="dropdown-item">Trainer</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    {{--Trainer Box--}}
                    <div id="trainerbox" class="collapse mt-1">
                        <div class="d-flex">
                            <span id="trainerinfo" class="form-control "></span>
                            <input id="figureCounter" type="number" class="form-control w-25" name="figureCounter" value="{{ old('cycle') }}" placeholder="#Figures?">
                            <button id="startTrainer" type="button" class="btn btn-success btn-sm">Start</button>
                        </div>
                    </div>
                    <!-- Trainer Modal -->
                    <div class="modal fade" id="trainervideo" tabindex="-1" role="dialog" aria-labelledby="trainervideo" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content w-auto">
                                <div class="modal-header">
                                    <h4 id="modalTitle" class="modal-title"></h4>
                                </div>
                                <div class="modal-body">
                                    <video controls id="videobox" muted class="embed-responsive-item">
                                        Your browser does not support the video tag!
                                    </video>
                                </div>
                                <div class="modal-footer">
{{--                                    <button id="closeTrainer" type="button" class="btn btn-secondary">Close</button>--}}
                                    <button id="stopTrainer" type="button" class="btn btn-danger" data-dismiss="modal">Stop trainer</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>


    @if(count($videos)>0)

        {{--Paginator goes here, but you have to solve the url issue with the redirect --}}
        <div class="d-flex justify-content-center mt-2">{{ $videos->links() }}</div>

        <div class="row row-cols-1 row-cols-md-3 mt-3">
            @foreach($videos as $video)

            <div class="col mb-4 video" data-href="{{ route('admin.videos.show', $video->id) }}">
                <div class="card h-100 border-secondary">
                    <div class="embed-responsive embed-responsive-4by3">
                        {{--option menu for each video--}}
                        @can('manage-app')
                            <div class="actions d-flex flex-column justify-content-between p-3">
                                <button type="button" class="btn btn-sm btn-dark"><strong>...</strong></button>
                                <div class="buttons d-none flex-column align-items-stretch">
                                    <a href="{{ route('admin.videos.edit', $video->id) }}" class="btn btn-sm btn-secondary mt-1 w-100">Edit</a>
                                    <form action="{{ action('Admin\VideoController@destroy', $video) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-secondary mt-1 w-100">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endcan
                        @if($video->timelapse)
                            <video id="video_{{ $video->id }}" muted class="card-img-top embed-responsive-item"><source src="/storage/videos/{{ $video->timelapse }}" type="video/webm" >
                                Your browser does not support the video tag!
                            </video>
                        @else
                            <img class="card-img-top embed-responsive-item" src="{{ asset('images/novideo.jpg') }}">
                        @endif
                    </div>
                    <h5 class="card-header">{{ $video->title }}
                        @if($video->sound)<button id="sound_{{ $video->id }}" class="btn btn-success btn-sm float-right soundbox ml-1" title="Click here to here the title in Spanish language!">&#128362;</button>
                        @endif
                        @can('manage-app')
                            <button id="publish_{{ $video->id }}" class="btn btn-info btn-sm float-right publishbutton" title="Click here to publish the video for the students!">
                            @if($video->published === 1)Published
                            @else Unpublished
                            @endif
                            </button>
                        @endcan
                    </h5>
                    <div class="card-body">
                        {{--Textbox will be constructed here--}}
                        <small class="d-block card-text overflow-hidden textbox">{!! $video->description !!}</small>
                        <p class="card-text">
                            {{--Rating stars goes here!--}}
                            <span id="progress_index_{{ $video->id }}" class="d-none progress_index" data-index="

                                {{ $video->users->first()->pivot->progress_index ?? '' }}
                                "></span>
                            @for($i=0; $i<5; $i++)
                                <span class="voting_stars text-secondary" data-index="{{ $i+1 }}" title="This is your progress bar for this video. T makes that video available for the trainer-mode!">
                                    @if($i < 4)
                                        &#10022;
                                    @else
                                        &#932;
                                    @endif
                                </span>
                            @endfor
                        </p>
                    </div>
                    <div class="card-footer text-muted text-center p-0 m-0">
                        <small>created {{ $video->created_at->diffInDays() !== 0 ? $video->created_at->diffInDays()." day(s) ago" : 'today' }} by {{ $video->videocreator->name }}</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        {{--Paginator--}}
        <div class="d-flex justify-content-center mt-2">{{ $videos->links() }}</div>

    @else
        <div class="row">
        <h5 class="bg-dark text-center mt-5 mx-auto p-2">Nothing to display, please upload videos!</h5>
        </div>
    @endif

@endsection
