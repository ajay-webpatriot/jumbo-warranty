<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544198035UsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            
if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
                }
if (!Schema::hasColumn('users', 'address_1')) {
                $table->string('address_1')->nullable();
                }
if (!Schema::hasColumn('users', 'address_2')) {
                $table->string('address_2')->nullable();
                }
if (!Schema::hasColumn('users', 'location_address')) {
                $table->string('location_address')->nullable();
                $table->double('location_latitude')->nullable();
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('address_1');
            $table->dropColumn('address_2');
            $table->dropColumn('location_address');
            $table->dropColumn('location_latitude');
            $table->dropColumn('location_longitude');
            
        });

    }
}
