<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544444676ServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            
if (!Schema::hasColumn('service_requests', 'call_type')) {
                $table->enum('call_type', array('AMC', 'Chargeable', 'FOC', 'Warranty'));
                }
if (!Schema::hasColumn('service_requests', 'serial_no')) {
                $table->string('serial_no');
                }
if (!Schema::hasColumn('service_requests', 'service_charge')) {
                $table->string('service_charge')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'adavance_amount')) {
                $table->string('adavance_amount')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'purchase_from')) {
                $table->string('purchase_from')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'complain_details')) {
                $table->text('complain_details')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'note')) {
                $table->string('note')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'call_location')) {
                $table->enum('call_location', array('On site', 'In House'));
                }
if (!Schema::hasColumn('service_requests', 'make')) {
                $table->string('make')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'service_tag')) {
                $table->string('service_tag')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'bill_no')) {
                $table->string('bill_no')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'mop')) {
                $table->enum('mop', array('Cash', 'Bank', 'Online', 'Credit / Debit Card'));
                }
if (!Schema::hasColumn('service_requests', 'bill_date')) {
                $table->string('bill_date')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'model_no')) {
                $table->string('model_no')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'priority')) {
                $table->enum('priority', array('HIGH', 'LOW', 'MEDIUM', 'MODERATE'));
                }
if (!Schema::hasColumn('service_requests', 'email')) {
                $table->string('email')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'installation_charge')) {
                $table->string('installation_charge')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'km_charge')) {
                $table->string('km_charge')->nullable();
                }
if (!Schema::hasColumn('service_requests', 'km_distance')) {
                $table->string('km_distance')->nullable();
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
            $table->dropColumn('call_type');
            $table->dropColumn('serial_no');
            $table->dropColumn('service_charge');
            $table->dropColumn('adavance_amount');
            $table->dropColumn('purchase_from');
            $table->dropColumn('complain_details');
            $table->dropColumn('note');
            $table->dropColumn('call_location');
            $table->dropColumn('make');
            $table->dropColumn('service_tag');
            $table->dropColumn('bill_no');
            $table->dropColumn('mop');
            $table->dropColumn('bill_date');
            $table->dropColumn('model_no');
            $table->dropColumn('priority');
            $table->dropColumn('email');

            $table->dropColumn('installation_charge');
            $table->dropColumn('km_charge');
            $table->dropColumn('km_distance');
            
        });

    }
}
