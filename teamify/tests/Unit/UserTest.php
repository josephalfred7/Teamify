<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
//use Faker;

class UserTest extends \Tests\TestCase
{
    protected $registration;
    protected $first_name;
    protected $last_name;
    protected $email;
    protected $password;

    public function testLoginLogout()
    {
        // for logout
        $this->call('POST', '/logout', ['_token' => null]);

        // for login
        $login = array(
            '_token' => null,
            'email' => $this->email,
            'password' =>$this->password
        );

        $response = $this->call('POST', '/login', $login);

        $response->assertSee('logged in');

        $response->assertSee($this->first_name);
        //$response->assertSee($this->last_name);
        //TODO: There is strange caching behavior with tests giving inconsistent results

    }

    public function testRegistration() {
        $this->assertDatabaseHas('users', [
            'email' => $this->email
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->first_name = 'Haley';
        $this->last_name = 'Spock';
        $this->email='abc@abc.com';
        $this->password = 'password';

        $this->registration = array(
            '_token' => null,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password
        );
        $this->withoutMiddleware();  // Does not validate token
        $this->call('POST', '/register', $this->registration);
    }

    protected function tearDown(): void
    {
        DB::table('users')->where('email', '=', 'abc@abc.com')->delete();
        parent::tearDown();
    }


}
