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
                                <table class="table table-sm  table_card">
                                    <thead class="table-info">
                                    <tr>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
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
                                                        <textarea class="input_change w-100" name="category_description">{{ $category->description }}</textarea>
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
                        <div class="card-header" id="userAccordion" data-toggle="collapse" data-target="#user">
                            <h5>Users</h5>
                        </div>
                        <div id="user" class="collapse" aria-labelledby="userAccordion" data-parent="#dashboardAccordion">
                            <div class="card-body">
                                <table class="table table-sm  table_card">
                                    <thead class="table-info">
                                    <tr>
                                        <th>Username</th>
                                        <th>E-Mail</th>
                                        <th></th>
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
