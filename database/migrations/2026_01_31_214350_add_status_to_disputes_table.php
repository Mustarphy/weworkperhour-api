<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToDisputesTable extends Migration
{
    public function up()
    {
        Schema::table('disputes', function (Blueprint $table) {
            // Add 'status' column after 'priority'
            $table->string('status')->default('Pending')->after('priority');
        });
    }

    public function down()
    {
        Schema::table('disputes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
