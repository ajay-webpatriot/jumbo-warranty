<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544448850AssignProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_products', function (Blueprint $table) {
            if(Schema::hasColumn('assign_products', 'product_id')) {
                $table->dropForeign('238997_5c0e61b78a9ce');
                $table->dropIndex('238997_5c0e61b78a9ce');
                $table->dropColumn('product_id');
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
                        
        });

    }
}
