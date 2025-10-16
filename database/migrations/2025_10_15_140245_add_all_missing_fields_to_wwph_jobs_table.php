<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('wwph_jobs', function (Blueprint $table) {
            // Strings with default values
            $table->string('title')->default('Untitled Job')->change();
            $table->text('description')->default('')->change();
            $table->text('requirements')->nullable()->change();
            $table->unsignedBigInteger('work_type')->default(1)->change();
            $table->unsignedBigInteger('job_type')->default(1)->change();
            $table->unsignedBigInteger('category')->default(1)->change();
            $table->string('salary')->default('Monthly')->change();
            $table->string('budget')->default('0')->change();
            $table->string('experience')->default('')->change();
            $table->string('job_cover')->default('')->change();
            $table->text('skills')->nullable()->change();
            $table->string('city')->default('')->change();
            $table->string('state')->default('')->change();
            $table->string('country')->default('')->change();
            $table->text('naration')->nullable()->change();
            $table->string('status')->default('active')->change();
        });
    }

    public function down()
    {
        Schema::table('wwph_jobs', function (Blueprint $table) {
            // Revert changes if needed
            $table->string('title')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->text('requirements')->nullable()->change();
            $table->unsignedBigInteger('work_type')->nullable()->change();
            $table->unsignedBigInteger('job_type')->nullable()->change();
            $table->unsignedBigInteger('category')->nullable()->change();
            $table->string('salary')->nullable()->change();
            $table->string('budget')->nullable()->change();
            $table->string('experience')->nullable()->change();
            $table->string('job_cover')->nullable()->change();
            $table->text('skills')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->text('naration')->nullable()->change();
            $table->string('status')->nullable()->change();
        });
    }
};
