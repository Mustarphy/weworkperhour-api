<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkApprovalToEmployerPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employer_payments', function (Blueprint $table) {
            $table->enum('work_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->text('employer_note')->nullable()->after('work_status'); // For rejection reason
            $table->timestamp('work_approved_at')->nullable()->after('employer_note');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employer_payments', function (Blueprint $table) {
            $table->dropColumn(['work_status', 'employer_note', 'work_approved_at']);
        });
    }
}
