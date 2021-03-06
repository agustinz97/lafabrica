<?php

use Illuminate\Database\Seeder;

class NewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i=0; $i < 10; $i++) {

            DB::table('news')->insert([
                'title' => $faker->sentence(),
                'body' => $faker->realText(),
                'created_at' => now()
            ]);

        }
    }
}
