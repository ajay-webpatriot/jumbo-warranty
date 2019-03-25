<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544438710CustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            
if (!Schema::hasColumn('customers', 'address_1')) {
                $table->string('address_1')->nullable();
                }
if (!Schema::hasColumn('customers', 'address_2')) {
                $table->string('address_2')->nullable();
                }
if (!Schema::hasColumn('customers', 'city')) {
                $table->string('city')->nullable();
                }
if (!Schema::hasColumn('customers', 'state')) {
                $table->string('state')->nullable();
                }
if (!Schema::hasColumn('customers', 'zipcode')) {
                $table->string('zipcode')->nullable();
                }
/*if (!Schema::hasColumn('customers', 'location')) {
                $table->string('location');
                }
        });*/
if (!Schema::hasColumn('customers', 'location_latitude')) {
                $table->double('location_latitude')->nullable();  
                }
if (!Schema::hasColumn('customers', 'location_longitude')) {
                $table->double('location_longitude')->nullable();
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
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('address_1');
            $table->dropColumn('address_2');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('zipcode');
            /*$table->dropColumn('location');*/
            $table->dropColumn('location_latitude');
            $table->dropColumn('location_longitude');
        });

    }
}
