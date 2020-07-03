@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1>Student List</h1>
                @foreach($users as $user)
                    <p>{{ ucwords($user->first_name) }} {{ ucwords($user->last_name) }}</p>
                @endforeach
            </div>
        </div>
    </div>
@endsection
