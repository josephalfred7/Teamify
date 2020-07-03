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
        $registration = $ur->getRegistration();
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

    public function testAlphaStudentList()
    {
        $registrars = array(
            new UserRegistrar(),
            new UserRegistrar(),
            new UserRegistrar()
        );

        foreach($registrars as $r) {
            $this->call('POST', '/register', $r->getRegistration());
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

    protected function tearDown(): void
    {
        parent::tearDown();
    }

}
