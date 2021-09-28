<?php

use Illuminate\Database\Seeder;

use Database\Seeders\DemoUserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DemoUserSeeder::class);
    }
}
