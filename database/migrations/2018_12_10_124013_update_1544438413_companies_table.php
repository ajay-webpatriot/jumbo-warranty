<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544438413CompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            
if (!Schema::hasColumn('companies', 'address_1')) {
                $table->string('address_1')->nullable();
                }
if (!Schema::hasColumn('companies', 'address_2')) {
                $table->string('address_2')->nullable();
                }
if (!Schema::hasColumn('companies', 'city')) {
                $table->string('city')->nullable();
                }
if (!Schema::hasColumn('companies', 'state')) {
                $table->string('state')->nullable();
                }
if (!Schema::hasColumn('companies', 'zipcode')) {
                $table->string('zipcode')->nullable();
                }
/*if (!Schema::hasColumn('companies', 'location')) {
                $table->string('location');
                } */
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('address_1');
            $table->dropColumn('address_2');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('zipcode');
            /*$table->dropColumn('location');*/
            
        });

    }
}
