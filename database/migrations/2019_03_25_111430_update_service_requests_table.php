<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateServiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'invoice_number')) {
                    $table->string('invoice_number')->nullable();
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
        

        if (Schema::hasColumn('service_requests', 'invoice_number'))
        {
            Schema::table('service_requests', function (Blueprint $table) {
                $table->dropColumnIfExists('invoice_number');
            });
        }
    }
}
