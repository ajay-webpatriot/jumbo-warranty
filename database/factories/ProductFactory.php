<?php

$factory->define(App\Product::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name,
        "category_id" => factory('App\Category')->create(),
        "price" => $faker->randomNumber(2),
        "status" => collect(["Active","Inactive",])->random(),
    ];
});
