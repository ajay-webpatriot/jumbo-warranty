<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Drop5c0e3e52e46b45c0e3e52e1ee0ProductProductPartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_product_part');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(! Schema::hasTable('product_product_part')) {
            Schema::create('product_product_part', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id', 'fk_p_237822_237835_produc_5c0a8822e4b8c')->references('id')->on('products');
                $table->integer('product_part_id')->unsigned()->nullable();
            $table->foreign('product_part_id', 'fk_p_237835_237822_produc_5c0a8822e5cc2')->references('id')->on('product_parts');
                
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }
}
