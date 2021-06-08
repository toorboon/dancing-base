<?php

use App\Role;
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
        $roleAdmin = Role::where('name', 'Admin')->first()->id;
        $initialUser = new User();
        $initialUser->role_id = $roleAdmin;
        $initialUser->name = ('testman');
        $initialUser->email = ('horstman@gmail.com');
        $initialUser->password = bcrypt('admin');
        $initialUser->save();
    }
}
