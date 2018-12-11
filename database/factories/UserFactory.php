<?php

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        "role_id" => factory('App\Role')->create(),
        "company_id" => factory('App\Company')->create(),
        "service_center_id" => factory('App\ServiceCenter')->create(),
        "name" => $faker->name,
        "phone" => $faker->name,
        "address_1" => $faker->name,
        "address_2" => $faker->name,
        "city" => $faker->name,
        "state" => $faker->name,
        "zipcode" => $faker->name,
        "email" => $faker->safeEmail,
        "password" => str_random(10),
        "remember_token" => $faker->name,
        "status" => collect(["Active","Inactive",])->random(),
    ];
});
