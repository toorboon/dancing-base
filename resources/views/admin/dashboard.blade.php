@extends('layouts.app')

@section('content')
    <div class="row">
        <ul class="navbar-nav mx-auto mb-3">
            <li class="nav-item dropdown text-center">
                <a id="actionDropdown" class="btn btn-dark dropdown-toggle border-secondary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" v-pre>
                    Actions <span class="caret"></span>
                </a>
                <div class="dropdown-menu" aria-labelledby="actionDropdown">
                    <a class="dropdown-item" href="{{ route('admin.categories.create') }}">Create Category</a>
                    <a class="dropdown-item" href="{{ route('register') }}">Create User</a>
                    <a class="dropdown-item" href="{{ route('change.password') }}">Change Password</a>
                </div>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="bs-example w-100">
                <div class="accordion" id="dashboardAccordion">

                    <div class="card">
                        <div class="card-header" id="categoryAccordion" data-toggle="collapse" data-target="#category">
                            <h5>Categories</h5>
                        </div>

                        <div id="category" class="collapse" aria-labelledby="categoryAccordion" data-parent="#dashboardAccordion">
                            <div class="card-body">
                                <table class="table  table-sm table-responsive table_card w-auto">
                                    <thead class="table-info">
                                    <tr>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody class="">
                                    @if (count($categories) > 0)
                                        @foreach($categories as $category)
                                            <tr>
                                                <td data-label="Title">
                                                    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="text" class="input_change" name="category_title" value="{{ $category->title }}" required>
                                                    </form>
                                                </td>

                                                <td data-label="Description">
                                                    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="input_change">
                                                            <textarea id="categoryDesc{{ $category->id }}" class="w-100 ckeditor" name="category_description">{{ $category->description }}</textarea>
                                                        </div>
                                                    </form>
                                                </td>

                                                <td class="d-flex">
                                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" value="delete">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No Categories found!</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="taglistAccordion" data-toggle="collapse" data-target="#taglist">
                            <h5>Tags</h5>
                        </div>

                        <div id="taglist" class="collapse" aria-labelledby="taglistAccordion" data-parent="#dashboardAccordion">
                            <div class="card-body">
                                <table class="table table-sm  table_card">
                                    <thead class="table-info">
                                    <tr>
                                        <th>Name</th>
                                        <th>Created</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($tags) > 0)
                                        @foreach($tags as $tag)
                                            <tr>
                                                <td data-label="Name">
                                                    <form action="{{ route('admin.dashboard.update', $tag) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input id="renameTags" type="text" class="input_change" name="tag_name" value="{{ $tag->name }}" required>
                                                    </form>
                                                </td>
                                                <td class="align-middle" data-label="created_date">{{ $tag->created_at }}</td>
                                                <td class="">
                                                    <form action="{{ action('Admin\DashboardController@destroy', $tag) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button id="deleteTags" type="submit" class="btn btn-sm btn-danger" value="delete">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No Tags found!</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="userAccordion" data-toggle="collapse" data-target="#user">
                            <h5>Users</h5>
                        </div>
                        <div id="user" class="collapse accordion_marker" aria-labelledby="userAccordion" data-parent="#dashboardAccordion">
                            <div class="card-body">
                                <table class="table table-sm  table_card">
                                    <thead class="table-info">
                                    <tr>
                                        <th>Username</th>
                                        <th>E-Mail</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (count($users) > 0)
                                        @foreach($users as $user)
                                            <tr>
                                                <td data-label="User Name">
                                                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="text" class="input_change" name="name" value="{{ $user->name }}" required>
                                                    </form>
                                                </td>
                                                <td data-label="User Email">
                                                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="email" class="input_change" name="email" value="{{ $user->email }}" required>
                                                    </form>
                                                </td>
                                                <td data-label="Role">
                                                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <select id="role" class="input_change mt-1" name="role">
                                                            <option value="1" @if($user->role->name == "Admin") selected @endif>Admin</option>
                                                            <option value="2" @if($user->role->name == "Guest") selected @endif>Guest</option>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td class="d-flex">
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" value="delete">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No Users found!</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
