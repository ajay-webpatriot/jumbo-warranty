<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add5c0e5ca7ce20bRelationshipsToServiceRequestTable extends Migration
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
                if (!Schema::hasColumn('service_requests', 'service_center_id')) {
                $table->integer('service_center_id')->unsigned()->nullable();
                $table->foreign('service_center_id', '237843_5c0a9653c53a0')->references('id')->on('service_centers')->onDelete('cascade');
                }
                if (!Schema::hasColumn('service_requests', 'product_id')) {
                $table->integer('product_id')->unsigned()->nullable();
                $table->foreign('product_id', '237843_5c0e5b052aced')->references('id')->on('products')->onDelete('cascade');
                }
                if (!Schema::hasColumn('service_requests', 'technician_id')) {
                $table->integer('technician_id')->unsigned()->nullable();
                $table->foreign('technician_id', '237843_5c0a9653e1125')->references('id')->on('users')->onDelete('cascade');
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
