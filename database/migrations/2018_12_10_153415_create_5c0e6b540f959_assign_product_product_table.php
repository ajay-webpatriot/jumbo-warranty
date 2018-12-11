<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create5c0e6b540f959AssignProductProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(! Schema::hasTable('assign_product_product')) {
            Schema::create('assign_product_product', function (Blueprint $table) {
                $table->integer('assign_product_id')->unsigned()->nullable();
                $table->foreign('assign_product_id', 'fk_p_238997_237822_produc_5c0e6b540fae3')->references('id')->on('assign_products')->onDelete('cascade');
                $table->integer('product_id')->unsigned()->nullable();
                $table->foreign('product_id', 'fk_p_237822_238997_assign_5c0e6b540fbd0')->references('id')->on('products')->onDelete('cascade');
                
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
        Schema::dropIfExists('assign_product_product');
    }
}
