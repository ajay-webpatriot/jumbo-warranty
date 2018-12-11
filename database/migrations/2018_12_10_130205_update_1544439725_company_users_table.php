<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544439725CompanyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_users', function (Blueprint $table) {
            
if (!Schema::hasColumn('company_users', 'email')) {
                $table->string('email');
                }
if (!Schema::hasColumn('company_users', 'username')) {
                $table->string('username');
                }
if (!Schema::hasColumn('company_users', 'password')) {
                $table->string('password');
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
        Schema::table('company_users', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('username');
            $table->dropColumn('password');
            
        });

    }
}
