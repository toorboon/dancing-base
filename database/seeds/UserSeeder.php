<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = ('testman');
        $user->email = ('horstman@gmail.com');
        $user->password = bcrypt('admin');
        $user->save();
    }
}
