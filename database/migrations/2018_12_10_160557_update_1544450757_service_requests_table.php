<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544450757ServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if(Schema::hasColumn('service_requests', 'serial_no')) {
                $table->dropColumn('serial_no');
            }
            if(Schema::hasColumn('service_requests', 'mop')) {
                $table->dropColumn('mop');
            }
            if(Schema::hasColumn('service_requests', 'service_charge')) {
                $table->dropColumn('service_charge');
            }
            
        });
Schema::table('service_requests', function (Blueprint $table) {
            
if (!Schema::hasColumn('service_requests', 'serial_no')) {
                $table->string('serial_no')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'mop')) {
                $table->enum('mop', array('Cash', 'Bank', 'Online', 'Credit / Debit Card'))->nullable();
                }
if (!Schema::hasColumn('service_requests', 'service_charge')) {
                $table->string('service_charge')->nullable();
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
            $table->dropColumn('serial_no');
            $table->dropColumn('mop');
            $table->dropColumn('service_charge');
            
        });
Schema::table('service_requests', function (Blueprint $table) {
                        $table->string('serial_no');
                $table->enum('mop', array('Cash', 'Bank', 'Online', 'Credit / Debit Card'));
                $table->string('service_charge')->nullable();
                
        });

    }
}
