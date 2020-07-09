<?php

namespace Tests\Unit;

use Tests\TestCase;
use UsersTableSeeder;

class UserControllerTest extends TestCase
{
    public function testGetUsers() {
        $uc = app('App\Http\Controllers\UserController');
        $students = $uc->getOrderedStudents();
        $seederStudentCount = UsersTableSeeder::$numStudents;

        $this->assertEquals($seederStudentCount, count($students));
    }

    public function testGetTeams() {
        $uc = app('App\Http\Controllers\UserController');
        $teams = $uc->getOrderedTeams();
        $seederTeamCount = UsersTableSeeder::$numTeams;

        $this->assertEquals($seederTeamCount, count($teams));
    }

    public function testAddTeam() {
        $uc = app('App\Http\Controllers\UserController');
        $teamName = "KnownName";
        $uc->addNamedTeam($teamName);
        $teamNames = $uc->getTeamNameArray();

        $this->assertTrue(in_array($teamName, $teamNames));
    }

}
