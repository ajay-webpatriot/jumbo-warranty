<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create5c0a9c31bb103ProductPartServiceRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(! Schema::hasTable('product_part_service_request')) {
            Schema::create('product_part_service_request', function (Blueprint $table) {
                $table->integer('product_part_id')->unsigned()->nullable();
                $table->foreign('product_part_id', 'fk_p_237835_237843_servic_5c0a9c31bb24f')->references('id')->on('product_parts')->onDelete('cascade');
                $table->integer('service_request_id')->unsigned()->nullable();
                $table->foreign('service_request_id', 'fk_p_237843_237835_produc_5c0a9c31bb2f2')->references('id')->on('service_requests')->onDelete('cascade');
                
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_part_service_request');
    }
}
