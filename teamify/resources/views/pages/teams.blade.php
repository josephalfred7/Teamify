@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1 id="TeamListHeader">Team List</h1>
            </div>
            <div class="col-md-6">
                @if(!Auth::guest() && Auth::user()->instructor == 1)
                <form method="POST" action="{{ route('pages.teams') }}">
                    @csrf
                    <div class="form-group row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button name="team_action" value="shuffle" type="submit" class="btn btn-primary shuffle">
                                {{ __('Shuffle') }}
                            </button>
                        </div>
                    </div>
                </form>
                @endif
            </div>
        </div>
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
