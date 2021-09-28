<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\User;

class DemoUserSeeder extends Seeder
{
    public static $GUEST = "guest";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->id = DemoUserSeeder::$GUEST;
        $user->name = DemoUserSeeder::$GUEST;
        $user->access_token = DemoUserSeeder::$GUEST;
        $user->refresh_token = DemoUserSeeder::$GUEST;
        $user->expires_at = '2030-1-1';
        $user->save();

    }
}
