<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update1544198872CompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            if(Schema::hasColumn('companies', 'company_admin_id')) {
                $table->dropForeign('237839_5c0a88716b4e1');
                $table->dropIndex('237839_5c0a88716b4e1');
                $table->dropColumn('company_admin_id');
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
        Schema::table('companies', function (Blueprint $table) {
                        
        });

    }
}
