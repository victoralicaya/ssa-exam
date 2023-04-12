@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card" style="width: 400px;">
                @if (!empty($user->photo))
                    <img class="card-img-top" src="{{ $user->avatar }}" alt="Card image">
                @else
                    <img class="card-img-top" src="{{ asset('storage/images/img_avatar1.png') }}" alt="Card image">
                @endif
                <div class="card-body">
                    <h4 class="card-title">Name: {{ $user->fullName }}</h4>
                    <h4 class="card-title">Email: {{ $user->email }}</h4>
                    <h4 class="card-title">Gender: {{ $user->details[3]->value }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
