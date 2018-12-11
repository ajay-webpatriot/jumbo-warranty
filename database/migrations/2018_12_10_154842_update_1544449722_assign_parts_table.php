<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544449722AssignPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_parts', function (Blueprint $table) {
            
if (!Schema::hasColumn('assign_parts', 'status')) {
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
        Schema::table('assign_parts', function (Blueprint $table) {
            $table->dropColumn('status');
            
        });

    }
}
