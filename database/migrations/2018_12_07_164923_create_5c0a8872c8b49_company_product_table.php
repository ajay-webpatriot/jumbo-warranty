<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create5c0a8872c8b49CompanyProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(! Schema::hasTable('company_product')) {
            Schema::create('company_product', function (Blueprint $table) {
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id', 'fk_p_237839_237822_produc_5c0a8872c8c4b')->references('id')->on('companies')->onDelete('cascade');
                $table->integer('product_id')->unsigned()->nullable();
                $table->foreign('product_id', 'fk_p_237822_237839_compan_5c0a8872c8cde')->references('id')->on('products')->onDelete('cascade');
                
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
        Schema::dropIfExists('company_product');
    }
}
