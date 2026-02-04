<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkStatusToMilestonesMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->enum('work_status', ['pending', 'submitted', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('status');
            $table->timestamp('work_submitted_at')->nullable()->after('work_status');
            $table->timestamp('work_approved_at')->nullable()->after('work_submitted_at');
            $table->text('employer_note')->nullable()->after('work_approved_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('milestones_migration', function (Blueprint $table) {
             $table->dropColumn(['work_status', 'work_submitted_at', 'work_approved_at', 'employer_note']);
        });
    }
}
