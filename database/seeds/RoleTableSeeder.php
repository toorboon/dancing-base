<?php

use App\Role;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleAdmin = new Role();
        $roleAdmin->name = ('Admin');
        $roleAdmin->description = ('Admin Users can do anything in the app, no restrictions!');
        $roleAdmin->save();

        $roleGuest = new Role();
        $roleGuest->name = ('Guest');
        $roleGuest->description = ('Used for pupils, so they can watch videos but change nothing in the app.');
        $roleGuest->save();
    }
}
