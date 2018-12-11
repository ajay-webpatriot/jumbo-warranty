<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Drop5c0a9c390690f5c0a9c3902e27ProductPartServiceRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_part_service_request');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(! Schema::hasTable('product_part_service_request')) {
            Schema::create('product_part_service_request', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('product_part_id')->unsigned()->nullable();
            $table->foreign('product_part_id', 'fk_p_237835_237843_servic_5c0a9c31b81da')->references('id')->on('product_parts');
                $table->integer('service_request_id')->unsigned()->nullable();
            $table->foreign('service_request_id', 'fk_p_237843_237835_produc_5c0a9c31b941e')->references('id')->on('service_requests');
                
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }
}
