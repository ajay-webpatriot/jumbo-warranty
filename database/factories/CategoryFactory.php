<?php

$factory->define(App\Category::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name,
        "service_charge" => $faker->randomNumber(2),
        "status" => collect(["Active","Inactive",])->random(),
    ];
});
