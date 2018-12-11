<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create5c0a8822e7271ProductProductPartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(! Schema::hasTable('product_product_part')) {
            Schema::create('product_product_part', function (Blueprint $table) {
                $table->integer('product_id')->unsigned()->nullable();
                $table->foreign('product_id', 'fk_p_237822_237835_produc_5c0a8822e74c6')->references('id')->on('products')->onDelete('cascade');
                $table->integer('product_part_id')->unsigned()->nullable();
                $table->foreign('product_part_id', 'fk_p_237835_237822_produc_5c0a8822e75d9')->references('id')->on('product_parts')->onDelete('cascade');
                
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
        Schema::dropIfExists('product_product_part');
    }
}
