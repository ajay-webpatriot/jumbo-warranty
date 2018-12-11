<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544449334UsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if(Schema::hasColumn('users', 'firstname')) {
                $table->dropColumn('firstname');
            }
            if(Schema::hasColumn('users', 'lastname')) {
                $table->dropColumn('lastname');
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
                        $table->string('firstname');
                $table->string('lastname');
                
        });

    }
}
