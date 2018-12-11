<?php

$factory->define(App\ManageCharge::class, function (Faker\Generator $faker) {
    return [
        "km_charge" => $faker->name,
        "status" => collect(["Active","Inactive",])->random(),
    ];
});
