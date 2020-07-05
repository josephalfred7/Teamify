<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\UserRegistrar;

class DisplayUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();  // Does not validate token
    }

    public function testUserDisplayed() {
        $ur = new UserRegistrar;
        $registration = $ur->getStudentRegistration();
        $this->call('POST', '/register', $registration);

        $response = $this->get('/users');
        $response->assertSee($registration['first_name']);
        $response->assertSee($registration['last_name']);

        $ur->deleteUser();
    }

    public function testStudentListRoute()
    {
        $response = $this->get('/users');
        $response->assertSee('Student List');
    }

    public function testTeamNameColumn()
    {
        $response = $this->get('/users');
        $response->assertSee('Team Name');
    }

    public function testAlphaStudentList()
    {
        $registrars = array(
            new UserRegistrar(),
            new UserRegistrar(),
            new UserRegistrar()
        );

        foreach($registrars as $r) {
            $this->call('POST', '/register', $r->getStudentRegistration());
        }

        $orderedNames = app('App\Http\Controllers\UserController')->getOrderedUsers();
        for($i = 0; $i < count($orderedNames); $i++)
        {
            if ($i < count($orderedNames) - 1) {
                $this->assertTrue(strcasecmp($orderedNames[$i + 1]->last_name, $orderedNames[$i]->last_name) >= 0);
            }
        }

        foreach($registrars as $r)
        {
            $r->deleteUser();
        }
    }



    public function testTeamDisplayed() {
        $ur = new UserRegistrar;
        $registration = $ur->getStudentRegistration();
        $this->call('POST', '/register', $registration);

        $uc = app('App\Http\Controllers\UserController');
        $uc->assignToTeam($registration['email'], 'Celtics');


        $response = $this->get('/users');
        $response->assertSee('Celtics');

        $ur->deleteUser();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

}
