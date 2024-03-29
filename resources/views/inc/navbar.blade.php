<nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route('index') }}">
            {{ config('app.name', 'Dancing Base') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}" title="Who is responsible for this thing?">About</a></li>
                @auth
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.videos.index') }}" title="Click here to see all the clips in the database">Videos</a></li>
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                @else

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            @can('manage-app')
                                <a class="dropdown-item" href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                                @if (Route::has('register'))
                                    <a class="dropdown-item" href="{{ route('register') }}">Create User</a>
                                @endif
                                <a class="dropdown-item" href="{{ route('admin.categories.create') }}">Create Category</a>
                                <a class="dropdown-item" href="{{ route('admin.videos.create') }}">Upload Video</a>
                            @endcan
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
