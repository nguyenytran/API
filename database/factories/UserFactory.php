<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

/**
 * @var \Illuminate\Database\Eloquent\Factory $factory
 */
$factory->define(\App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'email'                 => $faker->email,
        'name'                  => $faker->name,
        'username'              => $faker->userName,
        'password'              => \Illuminate\Support\Facades\Hash::make('password'),
        'gender'                => rand(1, 3),
        'address'               => $faker->address,
        'phone'                 => $faker->phoneNumber,
        'profile_picture'       => $faker->imageUrl('100'),
        'company_web'           => $faker->url,
        'is_active'             => 1,
        'created_by'            => 1,
    ];
});
