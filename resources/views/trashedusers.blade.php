@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
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

                        @if (count($trashedUsers['data']) > 0)
                            <tbody>
                                @foreach ($trashedUsers['data'] as $user)
                                    <tr>
                                        <td>{{ $user['prefixname'] }}</td>
                                        <td>{{ $user['firstname'] }}</td>
                                        <td>{{ $user['middlename'] }}</td>
                                        <td>{{ $user['lastname'] }}</td>
                                        <td>{{ $user['suffixname'] }}</td>
                                        <td>{{ $user['username'] }}</td>
                                        <td>{{ $user['email'] }}</td>
                                        <td>
                                            <div class="d-flex justify-content-start">
                                                <form action="{{ route('users.restore', $user['id']) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-info btn-sm" style="margin-right: 10px;">Restore</button>
                                                </form>
                                                <form action="{{ route('users.delete', $user['id']) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" {{ $user['id'] === auth()->user()->id ? 'disabled' : '' }}>Permanently Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                        <tbody>
                            <tr>
                                <td>No Data</td>
                                <td>No Data</td>
                                <td>No Data</td>
                                <td>No Data</td>
                                <td>No Data</td>
                                <td>No Data</td>
                                <td>No Data</td>
                                <td>No Action</td>
                            </tr>
                        </tbody>
                        @endif
                    </table>
                    @if (count($trashedUsers['data']) > 0)
                        <ul class="pagination justify-content-center">
                            @foreach ($trashedUsers['links'] as $link)
                                <li class="page-item {{ $link['active'] ? 'active' : ''}} {{ empty($link['url']) ? 'disabled' : '' }}"><a class="page-link" href="{{ $link['url'] }}">{{ html_entity_decode($link['label']) }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
