<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->init();
        $this->call(UsersTableSeeder::class);
    }

    /**
     * Init core user and setup default authentication for database seeder
     *
     * @return void
     */
    protected function init()
    {
        Artisan::call('api:install');

        $user = factory(User::class)->make([
            'email' => 'admin@api.asia',
            'name' => 'Administrator',
            'username' => 'admin',
        ]);
        User::unguarded(function () use (&$user) {
            $user->save();
            $user->syncRoles("admin");
        });

        Passport::actingAs($user, ['*']);
    }
}
