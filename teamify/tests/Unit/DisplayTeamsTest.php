<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\UserRegistrar;

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
