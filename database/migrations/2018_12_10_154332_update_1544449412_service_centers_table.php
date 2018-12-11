<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544449412ServiceCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_centers', function (Blueprint $table) {
            if(Schema::hasColumn('service_centers', 'addres_2')) {
                $table->dropColumn('addres_2');
            }
            
        });
Schema::table('service_centers', function (Blueprint $table) {
            
if (!Schema::hasColumn('service_centers', 'address_2')) {
                $table->string('address_2')->nullable();
                }
if (!Schema::hasColumn('service_centers', 'city')) {
                $table->string('city');
                }
if (!Schema::hasColumn('service_centers', 'state')) {
                $table->string('state');
                }
if (!Schema::hasColumn('service_centers', 'zipcode')) {
                $table->string('zipcode');
                }
if (!Schema::hasColumn('service_centers', 'status')) {
                $table->enum('status', array('Active', 'Inactive'))->nullable();
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
            $table->dropColumn('address_2');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('zipcode');
            $table->dropColumn('status');
            
        });
Schema::table('service_centers', function (Blueprint $table) {
                        $table->string('addres_2')->nullable();
                
        });

    }
}
