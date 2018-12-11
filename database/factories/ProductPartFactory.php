<?php

$factory->define(App\ProductPart::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name,
        "status" => collect(["Active","Inactive",])->random(),
    ];
});
