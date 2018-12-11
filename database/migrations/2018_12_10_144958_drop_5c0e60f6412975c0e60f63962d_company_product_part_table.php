<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Drop5c0e60f6412975c0e60f63962dCompanyProductPartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('company_product_part');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(! Schema::hasTable('company_product_part')) {
            Schema::create('company_product_part', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id', 'fk_p_237839_237835_produc_5c0a9a5db860b')->references('id')->on('companies');
                $table->integer('product_part_id')->unsigned()->nullable();
            $table->foreign('product_part_id', 'fk_p_237835_237839_compan_5c0a9a5db7a0b')->references('id')->on('product_parts');
                
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }
}
