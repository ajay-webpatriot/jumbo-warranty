<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544439475AssignPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assign_parts', function (Blueprint $table) {
            
if (!Schema::hasColumn('assign_parts', 'quantity')) {
                $table->integer('quantity')->nullable();
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
            $table->dropColumn('quantity');
            
        });

    }
}
