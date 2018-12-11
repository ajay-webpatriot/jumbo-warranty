<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add5c0a930f29f21RelationshipsToServiceCenterAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_center_admins', function(Blueprint $table) {
            if (!Schema::hasColumn('service_center_admins', 'service_center_id')) {
                $table->integer('service_center_id')->unsigned()->nullable();
                $table->foreign('service_center_id', '237850_5c0a930dabe56')->references('id')->on('service_centers')->onDelete('cascade');
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
        Schema::table('service_center_admins', function(Blueprint $table) {
            
        });
    }
}
