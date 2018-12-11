<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544199223ServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if(Schema::hasColumn('service_requests', 'status')) {
                $table->dropColumn('status');
            }
            
        });
Schema::table('service_requests', function (Blueprint $table) {
            
if (!Schema::hasColumn('service_requests', 'status')) {
                $table->enum('status', array('New', 'Assigned', 'Started', 'Pending for parts', 'Cancelled', 'Transferred to inhouse', 'Under testing', 'Issue for replacement', 'Closed'))->nullable();
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
            $table->dropColumn('status');
            
        });
Schema::table('service_requests', function (Blueprint $table) {
                        $table->enum('status', array('New', 'Assigned', 'Started', 'Pending for parts', 'Cancelled', 'Transferred to inhouse', 'Under testing', 'Issue for replacement', 'Closed'))->nullable();
                
        });

    }
}
