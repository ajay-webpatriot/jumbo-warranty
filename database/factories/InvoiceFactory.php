<?php

$factory->define(App\Invoice::class, function (Faker\Generator $faker) {
    return [
        "company_id" => factory('App\Company')->create(),
        "status" => collect(["Active","Inactive",])->random(),
    ];
});
