<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;

class UserTest extends \Tests\TestCase
{
    protected $registration;
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
    }

    public function testRegistration() {
        $this->assertDatabaseHas('users', [
            'email' => $this->email
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->email='abc@abc.com';
        $this->password = 'password';
        $this->registration = array(
            '_token' => null,
            'name' => 'fake name',
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
