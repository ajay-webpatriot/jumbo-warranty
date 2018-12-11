<?php

$factory->define(App\ServiceRequest::class, function (Faker\Generator $faker) {
    return [
        "company_id" => factory('App\Company')->create(),
        "customer_id" => factory('App\User')->create(),
        "service_type" => collect(["installation","repair",])->random(),
        "service_center_id" => factory('App\ServiceCenter')->create(),
        "technician_id" => factory('App\User')->create(),
        "call_type" => collect(["AMC","Chargeable","FOC","Warranty",])->random(),
        "call_location" => collect(["On site","In House",])->random(),
        "priority" => collect(["HIGH","LOW","MEDIUM","MODERATE",])->random(),
        "product_id" => factory('App\Product')->create(),
        "make" => $faker->name,
        "model_no" => $faker->name,
        "is_item_in_warrenty" => collect(["Yes","No",])->random(),
        "bill_no" => $faker->name,
        "bill_date" => $faker->name,
        "serial_no" => $faker->name,
        "mop" => collect(["Cash","Bank","Online","Credit / Debit Card",])->random(),
        "purchase_from" => $faker->name,
        "adavance_amount" => $faker->name,
        "service_charge" => $faker->name,
        "service_tag" => $faker->name,
        "complain_details" => $faker->name,
        "note" => $faker->name,
        "completion_date" => $faker->date("d-m-Y", $max = 'now'),
        "additional_charges" => $faker->randomNumber(2),
        "amount" => $faker->randomNumber(2),
        "status" => collect(["New","Assigned","Started","Pending for parts","Cancelled","Transferred to inhouse","Under testing","Issue for replacement","Closed",])->random(),
    ];
});
