<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add5c0a8f0014afdRelationshipsToServiceRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_requests', function(Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'company_id')) {
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id', '237843_5c0a8efe627a7')->references('id')->on('companies')->onDelete('cascade');
                }
                if (!Schema::hasColumn('service_requests', 'customer_id')) {
                $table->integer('customer_id')->unsigned()->nullable();
                $table->foreign('customer_id', '237843_5c0a8efe79f83')->references('id')->on('customers')->onDelete('cascade');
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
        Schema::table('service_requests', function(Blueprint $table) {
            
        });
    }
}
