@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <table class="table table-bordered table-striped table-dark">
                        <thead>
                            <tr>
                                <th>Prefix</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Suffx</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users['data'] as $user)
                                <tr>
                                    <td>{{ $user['prefixname'] }}</td>
                                    <td>{{ $user['firstname'] }}</td>
                                    <td>{{ $user['middlename'] }}</td>
                                    <td>{{ $user['lastname'] }}</td>
                                    <td>{{ $user['suffixname'] }}</td>
                                    <td>{{ $user['username'] }}</td>
                                    <td>{{ $user['email'] }}</td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('users.show', $user['id']) }}" class="btn btn-success btn-sm">Show</a>
                                            <a href="{{ route('users.edit', $user['id']) }}" class="btn btn-info btn-sm">Edit</a>
                                            <form action="{{ route('users.destroy', $user['id']) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" {{ $user['id'] === auth()->user()->id ? 'disabled' : '' }}>Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <ul class="pagination justify-content-center">
                        @foreach ($users['links'] as $link)
                            <li class="page-item {{ $link['active'] ? 'active' : ''}} {{ empty($link['url']) ? 'disabled' : '' }}"><a class="page-link" href="{{ $link['url'] }}">{{ html_entity_decode($link['label']) }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
