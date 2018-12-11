<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create1544196875ServiceCenterAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(! Schema::hasTable('service_center_admins')) {
            Schema::create('service_center_admins', function (Blueprint $table) {
                $table->increments('id');
                $table->string('firstname');
                $table->string('lastname');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                
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
        Schema::dropIfExists('service_center_admins');
    }
}
