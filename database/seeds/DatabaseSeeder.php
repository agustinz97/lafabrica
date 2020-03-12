<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        if(App::enviroment() === 'development'){
            $this->call(UsersTableSeeder::class);
            $this->call(ProjectsTableSeeder::class);
        }

        $this->call(PhotosTableSeeder::class);
        $this->call(NewsTableSeeder::class);
    }
}
