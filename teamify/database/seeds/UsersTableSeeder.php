<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $teams = ['-', $faker->colorName, $faker->colorName];

        for($i = 0; $i < 30; $i++) {
            DB::table('users')->insert([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->email,
                'team_name' => $teams[$i % count($teams)],
                'password' => $faker->password
            ]);
        }

        DB::table('users')->insert([
            'first_name' => 'Nancy',
            'last_name' => 'Smith',
            'email' => 'nancy@smith.com',
            'team_name' => '-',
            'password' => bcrypt('12345678')
        ]);

        DB::table('users')->insert([
            'first_name' =>'Professor',
            'last_name' => 'Plum',
            'email' => 'professor@plum.com',
            'team_name' => '-',
            'password' => bcrypt('12345678'),
            'instructor' => true
        ]);
    }
}
