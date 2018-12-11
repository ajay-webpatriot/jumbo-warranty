<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create5c0a9a5dbac35CompanyProductPartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(! Schema::hasTable('company_product_part')) {
            Schema::create('company_product_part', function (Blueprint $table) {
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id', 'fk_p_237839_237835_produc_5c0a9a5dbad68')->references('id')->on('companies')->onDelete('cascade');
                $table->integer('product_part_id')->unsigned()->nullable();
                $table->foreign('product_part_id', 'fk_p_237835_237839_compan_5c0a9a5dbae0a')->references('id')->on('product_parts')->onDelete('cascade');
                
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
        Schema::dropIfExists('company_product_part');
    }
}
