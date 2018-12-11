<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create1544196560ServiceCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(! Schema::hasTable('service_centers')) {
            Schema::create('service_centers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('address_1')->nullable();
                $table->string('addres_2')->nullable();
                $table->string('location_address');
                $table->double('location_latitude');
                $table->double('location_longitude');
                
                $table->timestamps();
                $table->softDeletes();

                $table->index(['deleted_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_centers');
    }
}
