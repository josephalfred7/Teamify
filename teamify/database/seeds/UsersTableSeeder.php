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
    public static $numStudents = 31;
    public static $numTeams = 3;
    public static $knownEmail = 'nancy@smith.com';

    public function run()
    {
        $faker = Faker\Factory::create();
        $teams = ['-'];
        for ($i = 0; $i < self::$numTeams - 1; $i++) {
            array_push($teams, $faker->unique()->colorName);
        }

        for($i = 0; $i < self::$numStudents - 1; $i++) {
            DB::table('users')->insert([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->email,
                'team_name' => $teams[$i % count($teams)],
                'password' => $faker->password
            ]);
        }

        DB::table('users')->insert([
            'first_name' => 'Nancy',
            'last_name' => 'Smith',
            'email' => self::$knownEmail,
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
