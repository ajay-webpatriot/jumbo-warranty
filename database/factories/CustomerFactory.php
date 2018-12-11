<?php

$factory->define(App\Customer::class, function (Faker\Generator $faker) {
    return [
        "firstname" => $faker->name,
        "lastname" => $faker->name,
        "phone" => $faker->name,
        "company_id" => factory('App\Company')->create(),
        "address_1" => $faker->name,
        "address_2" => $faker->name,
        "city" => $faker->name,
        "state" => $faker->name,
        "zipcode" => $faker->name,
        "location" => $faker->name,
        "status" => collect(["Active","Inactive",])->random(),
    ];
});
