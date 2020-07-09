<?php

namespace Tests\Unit;

use Tests\TestCase;
use UsersTableSeeder;

class UserControllerTest extends TestCase
{
    private $uc;

    protected function setUp(): void
    {
        $this->uc = app('App\Http\Controllers\UserController');
        parent::setUp();

    }

    public function testGetUsers() {
        $students = $this->uc->getOrderedStudents();
        $seederStudentCount = UsersTableSeeder::$numStudents;

        $this->assertEquals($seederStudentCount, count($students));
    }

    public function testGetTeams() {
        $teams =  $this->uc->getOrderedTeams();
        $seederTeamCount = UsersTableSeeder::$numTeams;

        $this->assertEquals($seederTeamCount, count($teams));
    }

    public function testAddTeam() {
        $teamName = "KnownName";
        $this->uc->addNamedTeam($teamName);
        $teamNames =  $this->uc->getTeamNameArray();

        $this->assertTrue(in_array($teamName, $teamNames));
    }

    public function testShuffleTeams(){
        $this->assertNotEquals(0,  $this->uc->getUnassignedStudentCount());
        $this->uc->shuffleTeams();

        $this->assertEquals(0,  $this->uc->getUnassignedStudentCount());
    }

    public function testShuffleTeamSet(){
        $teamNames =  $this->uc->getTeamNameArray();

        $this->assertEquals(0,  $this->uc->getUnassignedStudentCount());
        array_push($teamNames, '-');
        $this->uc->shuffleTeamSet($teamNames);
        $this->assertNotEquals(0,  $this->uc->getUnassignedStudentCount());
    }

    public function testGetOrderedStudentsNotInstructor(){
        $studentResult = $this->uc->getOrderedStudents();

        foreach($studentResult as $i => $student){
            $this->assertEquals(0, $student->instructor);
        }
    }

    public function testGetOrderedStudentsInAlphabeticalOrder(){
        $orderedNames = $this->uc->getOrderedStudents();
        for($i = 0; $i < count($orderedNames); $i++)
        {
            if ($i < count($orderedNames) - 1) {
                $this->assertTrue(strcasecmp($orderedNames[$i + 1]->last_name, $orderedNames[$i]->last_name) >= 0);
            }
        }
    }

    public function testGetOrderedTeamsInAlphabeticalOrder(){
        $orderedTeams = $this->uc->getOrderedTeams();
        for($i = 0; $i < count($orderedTeams); $i++)
        {
            if ($i < count($orderedTeams) - 1) {
                $this->assertTrue(strcasecmp($orderedTeams[$i + 1]['team'], $orderedTeams[$i]['team']) >= 0);
            }
        }
    }

    public function testOptimalTeamCount() {
        $this->assertEquals(0,$this->uc->getOptimalTeamCount(0));
        $this->assertEquals(1,$this->uc->getOptimalTeamCount(1));
        $this->assertEquals(1,$this->uc->getOptimalTeamCount(6));
        $this->assertEquals(2,$this->uc->getOptimalTeamCount(7));
        $this->assertEquals(2,$this->uc->getOptimalTeamCount(11));
        $this->assertEquals(3,$this->uc->getOptimalTeamCount(12));
        $this->assertEquals(4,$this->uc->getOptimalTeamCount(17));
        $this->assertEquals(5,$this->uc->getOptimalTeamCount(22));
        $this->assertEquals(6,$this->uc->getOptimalTeamCount(28));
        $this->assertEquals(7,$this->uc->getOptimalTeamCount(36));
    }

    public function testAssignToTeam() {
        $this->uc->assignToTeam(UsersTableSeeder::$knownEmail, '-');
        $unassigned = $this->uc->getUnassignedStudentCount();
        $newTeam = $this->uc->getTeamNameArray()[0];
        $this->uc->assignToTeam(UsersTableSeeder::$knownEmail, $newTeam);
        $newUnassigned = $this->uc->getUnassignedStudentCount();
        $this->assertEquals($unassigned - 1, $newUnassigned);
    }
}
