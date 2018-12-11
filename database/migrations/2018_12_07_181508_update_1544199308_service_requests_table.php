<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544199308ServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            
if (!Schema::hasColumn('service_requests', 'additional_charges')) {
                $table->decimal('additional_charges', 15, 2)->nullable();
                }
if (!Schema::hasColumn('service_requests', 'amount')) {
                $table->decimal('amount', 15, 2)->nullable();
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
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn('additional_charges');
            $table->dropColumn('amount');
            
        });

    }
}
