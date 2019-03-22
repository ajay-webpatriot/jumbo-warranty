<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAssignProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_products', function (Blueprint $table) {
            if (!Schema::hasColumn('assign_products', 'product_id')) {
                $table->integer('product_id')->unsigned()->nullable();
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assign_products', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
}
