<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544438135ServiceCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_centers', function (Blueprint $table) {
            
if (!Schema::hasColumn('service_centers', 'commission')) {
                $table->integer('commission')->nullable();
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
        Schema::table('service_centers', function (Blueprint $table) {
            $table->dropColumn('commission');
            
        });

    }
}
