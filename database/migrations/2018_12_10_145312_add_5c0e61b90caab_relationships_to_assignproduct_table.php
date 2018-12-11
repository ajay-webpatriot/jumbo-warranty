<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add5c0e61b90caabRelationshipsToAssignProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_products', function(Blueprint $table) {
            if (!Schema::hasColumn('assign_products', 'product_id')) {
                $table->integer('product_id')->unsigned()->nullable();
                $table->foreign('product_id', '238997_5c0e61b78a9ce')->references('id')->on('products')->onDelete('cascade');
                }
                if (!Schema::hasColumn('assign_products', 'company_id')) {
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id', '238997_5c0e61b7a123d')->references('id')->on('companies')->onDelete('cascade');
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
        Schema::table('assign_products', function(Blueprint $table) {
            
        });
    }
}
