<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544439181ServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            
if (!Schema::hasColumn('service_requests', 'is_item_in_warrenty')) {
                $table->enum('is_item_in_warrenty', array('Yes', 'No'));
                }
if (!Schema::hasColumn('service_requests', 'completion_date')) {
                $table->date('completion_date')->nullable();
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
            $table->dropColumn('is_item_in_warrenty');
            $table->dropColumn('completion_date');
            
        });

    }
}
