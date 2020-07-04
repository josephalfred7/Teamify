@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Student List</h1>
        <div class="row justify-content-center">

            <div class="col-md-4">
                <h3>Name</h3>
                @foreach($users as $user)
                    <p>{{ ucwords($user->first_name) }} {{ ucwords($user->last_name) }}</p>
                @endforeach
            </div>
            <div class="col-md-8">
                <h3>Team Name</h3>
                @foreach($users as $user)
                    <p>{{ ucwords($user->team_name) }}</p>
                @endforeach
            </div>

        </div>
    </div>
@endsection
