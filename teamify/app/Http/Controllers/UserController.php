<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getUsers() {
        return view('pages.users')->with([
            'users' => $this->getOrderedUsers()
        ]);
    }

    public function getOrderedUsers()
    {
        return DB::table('users')->orderBy('last_name')->get();
    }
}
