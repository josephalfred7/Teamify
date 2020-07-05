@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 id="TeamListHeader">Team List</h1>
        <div class="row">
            <div class="col-md-12">
                @foreach($teamRosters as $teamRoster)
                    <h4 class="TeamHeader">
                        @if($teamRoster['team']=="-")
                            NO TEAM ({{ $teamRoster['count'] }})
                        @else
                            {{ $teamRoster['team'] }} ({{ $teamRoster['count'] }})
                        @endif
                    </h4>
                     @foreach($teamRoster['members'] as $i => $member)
                        <p>{{ ucwords($member->first_name) }} {{ ucwords($member->last_name) }}</p>
                     @endforeach
                     <br>
                @endforeach
            </div>
        </div>
    </div>
@endsection
