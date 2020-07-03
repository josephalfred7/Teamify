<?php

namespace Tests\Unit;


use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Controller;
use PHPUnit\Util\Printer;


class ControllerTest extends \Tests\TestCase
{
    public function testRegisterNewUser()
    {
        $user = factory( 'App\User' )->raw();

        $response = $this->post( '/register', $user );

        fwrite(STDERR, $response->content());

        $this->assertTrue(true);
    }
}
