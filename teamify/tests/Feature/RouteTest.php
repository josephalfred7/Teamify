<?php


namespace Tests\Feature;


class RouteTest extends \Tests\TestCase
{

    public function testWelcome()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testRegister()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function testLogin()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function testUsers() {
        $response = $this->get('/users');

        $response->assertStatus(200);

    }

    public function testTeams() {
        $response = $this->get('/teams');

        $response->assertStatus(200);
    }

}
