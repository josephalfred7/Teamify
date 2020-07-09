<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\UserRegistrar;
use Illuminate\Support\Facades\DB;


class DisplayTeamsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();  // Does not validate token
    }

    public function testTeamListRoute()
    {
        $response = $this->get('/teams');
        $response->assertSee('Team List');
    }

    public function testAlphaTeamList()
    {
        $registrars = array(
            new UserRegistrar(),
            new UserRegistrar(),
            new UserRegistrar()
        );
        $uc = app('App\Http\Controllers\UserController');

        foreach($registrars as $r) {
            $this->call('POST', '/register', $r->getStudentRegistration());
            $uc->assignToTeam($r->email, $r->first_name);
        }

        $orderedTeams = $uc->getOrderedTeams();
        for($i = 0; $i < count($orderedTeams); $i++)
        {
            if ($i < count($orderedTeams) - 1) {
                $this->assertTrue(strcasecmp($orderedTeams[$i + 1]['team'], $orderedTeams[$i]['team']) >= 0);
            }
        }

        foreach($registrars as $r)
        {
            $r->deleteUser();
        }
    }

    public function testInstructorNotDisplayed() {
        $ur = new UserRegistrar;
        $registration = $ur->getInstructorRegistration();
        $this->call('POST', '/register', $registration);

        $response = $this->get('/teams');
        $full_name = $registration['first_name']." ".$registration['last_name'];
        $response->assertDontSee($full_name);

        $ur->deleteUser();
    }

    public function testInstructorSeesShuffleButton() {
        $ur = new UserRegistrar;
        $registration = $ur->getInstructorRegistration();
        $this->call('POST', '/register', $registration);

        $response = $this->get('/teams');
        $response->assertSee('Shuffle');

        $ur->deleteUser();
    }

    public function testStudentsDontSeeShuffleButton() {
        $ur = new UserRegistrar;
        $registration = $ur->getStudentRegistration();
        $this->call('POST', '/register', $registration);

        $response = $this->get('/teams');
        $response->assertDontSee('Shuffle');

        $ur->deleteUser();
    }

    public function testInstructorSeesAddTeamButton() {
        $ur = new UserRegistrar;
        $registration = $ur->getInstructorRegistration();
        $this->call('POST', '/register', $registration);

        $response = $this->get('/teams');
        $response->assertSee('Add Team');

        $ur->deleteUser();
    }

    public function testStudentsDontSeeAddTeamButton() {
        $ur = new UserRegistrar;
        $registration = $ur->getStudentRegistration();
        $this->call('POST', '/register', $registration);

        $response = $this->get('/teams');
        $response->assertDontSee('Add Team');

        $ur->deleteUser();
    }

    public function testInstructorSeesOptimizeButton() {
        $ur = new UserRegistrar;
        $registration = $ur->getInstructorRegistration();
        $this->call('POST', '/register', $registration);

        $response = $this->get('/teams');
        $response->assertSee('Optimize');

        $ur->deleteUser();
    }

    public function testStudentsDontSeeOptimizeButton() {
        $ur = new UserRegistrar;
        $registration = $ur->getStudentRegistration();
        $this->call('POST', '/register', $registration);

        $response = $this->get('/teams');
        $response->assertDontSee('Optimize');

        $ur->deleteUser();
    }

    public function testOptimizeAddsTeams(){
        $application = app('App\Http\Controllers\UserController');
        $teamCount = count($application->getTeamNames());
        $studentCount = count($application->getOrderedStudents());
        $regis = array();

        while(round($studentCount/5.0) <= $teamCount){
            $sr = new UserRegistrar;
            $registration = $sr->getStudentRegistration();
            $this->call('POST', '/register', $registration);

            array_push($regis, $sr);
            $studentCount++;
        }

        $this->call('POST', '/teams', ['team_action'=>'optimize']);

        $newTeamCount = count($application->getOrderedTeams());

        $this->assertGreaterThan($teamCount, $newTeamCount);

        foreach($regis as $r){
            $r->deleteUser();
        }
    }

    public function testShuffleAssignsAllStudents() {
        $sr = new UserRegistrar;
        $registration = $sr->getStudentRegistration();
        $this->call('POST', '/register', $registration);


        $ir = new UserRegistrar;
        $registration = $ir->getInstructorRegistration();
        $this->call('POST', '/register', $registration);

        $this->call('POST', '/teams', ['team_action'=>'shuffle']);
        $this->assertEquals(0, app('App\Http\Controllers\UserController')->getUnassignedStudentCount());

        $sr->deleteUser();
        $ir->deleteUser();

    }

    public function testOptimizeTeams() {
        $sr = new UserRegistrar;
        $registration = $sr->getStudentRegistration();
        $this->call('POST', '/register', $registration);


        $ir = new UserRegistrar;
        $registration = $ir->getInstructorRegistration();
        $this->call('POST', '/register', $registration);

        $this->call('POST', '/teams', ['team_action'=>'optimize']);
        $this->assertEquals(0, app('App\Http\Controllers\UserController')->getUnassignedStudentCount());

        $sr->deleteUser();
        $ir->deleteUser();

    }

    public function testTeamAdded() {
        $ir = new UserRegistrar;
        $registration = $ir->getInstructorRegistration();
        $this->call('POST', '/register', $registration);
        $teamNamesBefore = DB::table('users')->select('team_name')->distinct()->get();
        $teamCountBefore = count($teamNamesBefore);

        $this->call('POST', '/teams', ['team_action'=>'add_team']);

        $teamNamesAfter = DB::table('users')->select('team_name')->distinct()->get();
        $teamCountAfter = count($teamNamesAfter);

        $this->assertEquals($teamCountBefore + 1, $teamCountAfter);

        //TODO: how to iterate array of arrays to find if team names different
        //$differentTeams = diff($teamNamesAfter, $teamNamesBefore);

        $ir->deleteUser();
    }

    public function testTeamHeaderDisplayed() {
        $ur = new UserRegistrar;
        $registration = $ur->getStudentRegistration();
        $this->call('POST', '/register', $registration);

        $response = $this->get('/teams');
        $response->assertSee('TeamHeader');

        $ur->deleteUser();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
