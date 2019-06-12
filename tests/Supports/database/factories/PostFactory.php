<?php

use RichanFongdasen\Varnishable\Tests\Supports\Models\Post;

$factory->define(Post::class, function (\Faker\Generator $faker) {
    $time = random_int(1483203600, 1530378000);

    return [
        'title'       => $faker->sentence,
        'description' => $faker->paragraph(2),
        'content'     => implode("\n<br />\n", $faker->paragraphs(10)),
        'created_at'  => '2016-01-01 00:00:00',
        'updated_at'  => date('Y-m-d H:i:s', $time),
    ];
});
