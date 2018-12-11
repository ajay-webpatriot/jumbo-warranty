<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add5c0e6c244e0dcRelationshipsToAssignPartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_parts', function(Blueprint $table) {
            if (!Schema::hasColumn('assign_parts', 'company_id')) {
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id', '238938_5c0e466a8086d')->references('id')->on('companies')->onDelete('cascade');
                }
                if (!Schema::hasColumn('assign_parts', 'product_parts_id')) {
                $table->integer('product_parts_id')->unsigned()->nullable();
                $table->foreign('product_parts_id', '238938_5c0e466a6c0ac')->references('id')->on('product_parts')->onDelete('cascade');
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
        Schema::table('assign_parts', function(Blueprint $table) {
            
        });
    }
}
