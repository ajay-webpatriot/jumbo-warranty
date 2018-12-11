<?php

$factory->define(App\ServiceCenter::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name,
        "address_1" => $faker->name,
        "commission" => $faker->randomNumber(2),
        "address_2" => $faker->name,
        "city" => $faker->name,
        "state" => $faker->name,
        "zipcode" => $faker->name,
        "status" => collect(["Active","Inactive",])->random(),
    ];
});
