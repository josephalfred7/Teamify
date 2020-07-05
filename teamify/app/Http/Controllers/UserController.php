<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getUsers() {
        return view('pages.users')->with([
            'users' => $this->getOrderedUsers()
        ]);
    }

    public function getTeams() {
        return view('pages.teams')->with([
            'teamRosters' => $this->getOrderedTeams()
        ]);
    }

    public function getOrderedUsers()
    {
        return DB::table('users')->where('instructor', 0)->orderBy('last_name')->get();
    }

    public function getOrderedTeams()
    {
        $teamRosters = array();
        $teamNames = DB::table('users')->select('team_name')->distinct()->orderBy('team_name')->get();

        foreach($teamNames as $i => $team){
            $count = DB::table('users')->select('*')->where(['team_name' => $team->team_name,'instructor' => 0], 'and')->count();
            $members = DB::table('users')->select('first_name','last_name')->where(['team_name' => $team->team_name,'instructor' => 0], 'and')->orderBy('last_name')->get();

            array_push($teamRosters, [
               'team' => $team->team_name,
               'count' => $count,
                'members' => $members
            ]);
        }

        return $teamRosters;
    }

    public function assignToTeam($email, $team) {
        DB::table('users')->where('email', $email)->update(['team_name'=>$team]);
    }
}
