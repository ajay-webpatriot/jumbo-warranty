<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add5c0a9ac32073bRelationshipsToCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function(Blueprint $table) {
            if (!Schema::hasColumn('companies', 'company_admin_id')) {
                $table->integer('company_admin_id')->unsigned()->nullable();
                $table->foreign('company_admin_id', '237839_5c0a88716b4e1')->references('id')->on('users')->onDelete('cascade');
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
        Schema::table('companies', function(Blueprint $table) {
            
        });
    }
}
