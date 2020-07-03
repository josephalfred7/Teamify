<?php

namespace Tests;

use Illuminate\Support\Facades\DB;
use Faker;

class UserRegistrar
{
    private $faker;
    public $first_name;
    public $last_name;
    public $email;
    public $password;

    public function __construct() {
        $this->faker = Faker\Factory::create();
    }

    public function getRegistration() : array {
        $this->first_name = $this->faker->firstName;
        $this->last_name = $this->faker->lastName;
        $this->email = $this->faker->email;
        $this->password = $this->faker->password(8);

        $this->registration = array(
            '_token' => null,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password
        );

        return $this->registration;
    }

    public function deleteUser(): void
    {
        DB::table('users')->where('email', '=', $this->email)->delete();
    }
}
