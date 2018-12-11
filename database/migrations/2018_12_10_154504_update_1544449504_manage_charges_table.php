<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544449504ManageChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manage_charges', function (Blueprint $table) {
            
if (!Schema::hasColumn('manage_charges', 'status')) {
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
        Schema::table('manage_charges', function (Blueprint $table) {
            $table->dropColumn('status');
            
        });

    }
}
