<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add5c0e6fb3111a4RelationshipsToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->integer('role_id')->unsigned()->nullable();
                $table->foreign('role_id', '236021_5c06113d29202')->references('id')->on('roles')->onDelete('cascade');
                }
                if (!Schema::hasColumn('users', 'company_id')) {
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id', '236021_5c0a9716566a5')->references('id')->on('companies')->onDelete('cascade');
                }
                if (!Schema::hasColumn('users', 'service_center_id')) {
                $table->integer('service_center_id')->unsigned()->nullable();
                $table->foreign('service_center_id', '236021_5c0a971669e4a')->references('id')->on('service_centers')->onDelete('cascade');
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
        Schema::table('users', function(Blueprint $table) {
            
        });
    }
}
