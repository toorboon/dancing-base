<?php

use App\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = new Category();
        $category->title = ('Salsa');
        $category->description = ('The best dance you can imagine!');
        $category->save();

        $category = new Category();
        $category->title = ('Bachata');
        $category->description = ('If you want to have sex on the dancefloor, choose this dance!');
        $category->save();
    }
}
