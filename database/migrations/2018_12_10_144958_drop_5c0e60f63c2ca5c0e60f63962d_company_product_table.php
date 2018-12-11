<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Drop5c0e60f63c2ca5c0e60f63962dCompanyProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('company_product');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(! Schema::hasTable('company_product')) {
            Schema::create('company_product', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id', 'fk_p_237839_237822_produc_5c0a887151799')->references('id')->on('companies');
                $table->integer('product_id')->unsigned()->nullable();
            $table->foreign('product_id', 'fk_p_237822_237839_compan_5c0a887150836')->references('id')->on('products');
                
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }
}
