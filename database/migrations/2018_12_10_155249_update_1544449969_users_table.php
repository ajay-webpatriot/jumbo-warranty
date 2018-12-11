<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544449969UsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            
if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable();
                }
if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable();
                }
if (!Schema::hasColumn('users', 'zipcode')) {
                $table->string('zipcode')->nullable();
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
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('zipcode');
            
        });

    }
}
