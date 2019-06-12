<?php

use RichanFongdasen\Varnishable\Tests\Supports\Models\User;

$factory->define(User::class, function (\Faker\Generator $faker) {
    $time = random_int(1483203600, 1530378000);
    
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(12)),
        'remember_token' => str_random(12),
        'created_at' => '2016-01-01 00:00:00',
        'updated_at' => date('Y-m-d H:i:s', $time),
    ];
});
