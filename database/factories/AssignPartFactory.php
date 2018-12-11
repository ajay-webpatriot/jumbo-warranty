<?php

$factory->define(App\AssignPart::class, function (Faker\Generator $faker) {
    return [
        "company_id" => factory('App\Company')->create(),
        "product_parts_id" => factory('App\ProductPart')->create(),
        "quantity" => $faker->randomNumber(2),
        "status" => collect(["Active","Inactive",])->random(),
    ];
});
