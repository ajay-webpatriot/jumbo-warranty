<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544449754ProductPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_parts', function (Blueprint $table) {
            
if (!Schema::hasColumn('product_parts', 'status')) {
                $table->enum('status', array('Active', 'Inactive'));
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
        Schema::table('product_parts', function (Blueprint $table) {
            $table->dropColumn('status');
            
        });

    }
}
