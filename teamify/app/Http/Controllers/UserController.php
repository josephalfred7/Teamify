<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Faker;


class UserController extends Controller
{
    public function getUsers() {
        return view('pages.users')->with([
            'users' => $this->getOrderedStudents()
        ]);
    }

    public function getTeams() {
        return view('pages.teams')->with([
            'teamRosters' => $this->getOrderedTeams()
        ]);
    }

    public function postTeams(Request $data) {
        if($data->team_action == 'shuffle') {
            $this->shuffleTeams();
        }
        if($data->team_action == 'add_team') {
            $this->addTeam();
        }

        return $this->getTeams();
    }

    private function addTeam() {
        $faker = Faker\Factory::create();
        $newTeamName = $faker->colorName . random_int(0, 100);
        $this->addNamedTeam($newTeamName);
    }

    private function addNamedTeam($teamName) {
        $faker = Faker\Factory::create();
        DB::table('users')->insert([
            'first_name' => 'Dummy',
            'last_name' => 'Instructor',
            'email' => $faker->email,
            'team_name' => $teamName,
            'password' => $faker->password,
            'instructor' => 1
        ]);
    }

    private function shuffleTeams() {
        $teamNameResult = $this->getTeamNames();
        $teamNames = array();

        foreach($teamNameResult as $i => $team) {
            array_push($teamNames, $team->team_name);
        }

        $studentsResult = $this->getOrderedStudents();
        $emails = array();

        foreach($studentsResult as $i => $student) {
            array_push($emails, $student->email);
        }

        shuffle($emails);
        for($i=0;$i < count($emails);$i++) {
            $this->assignToTeam($emails[$i],$teamNames[$i % count($teamNames)]);
        }

    }

    private function getTeamNames() {
        $teamNames = DB::table('users')->select('team_name')->distinct()
            ->where('team_name', '<>', '-')
            ->orderBy('team_name')
            ->get();
        return $teamNames;
    }


    public function getOrderedStudents()
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

    public function getUnassignedStudentCount()
    {
        $count = DB::table('users')->select('*')->where(['team_name' => '-','instructor' => 0], 'and')->count();
        return $count;
    }

    public function assignToTeam($email, $team) {
        DB::table('users')->where('email', $email)->update(['team_name'=>$team]);
    }
}
