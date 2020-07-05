<?php

namespace Tests\Unit;

use Tests\UserRegistrar;

//use Faker;

class UserTest extends \Tests\TestCase
{
    protected $registration;
    protected $first_name;
    protected $last_name;
    protected $email;
    protected $password;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();  // Does not validate token
    }

    public function testLoginLogout()
    {
        $ur = new UserRegistrar;
        $registration = $ur->getStudentRegistration();
        $this->call('POST', '/register', $registration);

        // for logout
        $this->call('POST', '/logout', ['_token' => null]);

        // for login
        $login = array(
            //'_token' => null,
            'email' => $ur->email,
            'password' => $ur->password
        );

        $this->call('POST', '/login', $login);
        $response = $this->call('GET', '/home');
        $response->assertSee('logged in');
        $response->assertSee($ur->first_name);  // This used to not work?

        $ur->deleteUser();
    }

    public function testUserRegistration() {
        $ur = new UserRegistrar;
        $registration = $ur->getStudentRegistration();
        $this->call('POST', '/register', $registration);

        $this->assertDatabaseHas('users', [
            'email' => $ur->email,
            'instructor' => $ur->instructor
        ]);

        $ur->deleteUser();
    }

    public function testInstructorRegistration() {
        $ur = new UserRegistrar;
        $registration = $ur->getInstructorRegistration();
        $this->call('POST', '/register', $registration);

        $this->assertDatabaseHas('users', [
            'email' => $ur->email,
            'instructor' => $ur->instructor
        ]);

        $response = $this->call('GET', '/home');
        $response->assertSee('(Instructor)');

        $ur->deleteUser();
    }

    public function testInstructorCheckBox(){
        $response = $this->call('GET', '/register');
        $response->assertSee('I am an instructor');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }


}
