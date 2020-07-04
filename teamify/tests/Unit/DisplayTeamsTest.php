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
            $this->call('POST', '/register', $r->getRegistration());
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

    public function testTeamHeaderDisplayed() {
        $ur = new UserRegistrar;
        $registration = $ur->getRegistration();
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
