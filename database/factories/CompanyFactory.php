<?php

$factory->define(App\Company::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name,
        "credit" => $faker->name,
        "installation_charge" => $faker->randomNumber(2),
        "address_1" => $faker->name,
        "address_2" => $faker->name,
        "city" => $faker->name,
        "state" => $faker->name,
        "zipcode" => $faker->name,
        "location" => $faker->name,
        "status" => collect(["Active","Inactive",])->random(),
    ];
});
