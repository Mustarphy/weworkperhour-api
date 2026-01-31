<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('wwph_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('wwph_jobs', 'title')) {
                $table->string('title')->default('Untitled Job');
            } else {
                $table->string('title')->default('Untitled Job')->change();
            }

            if (!Schema::hasColumn('wwph_jobs', 'description')) {
                $table->text('description')->default('');
            } else {
                $table->text('description')->default('')->change();
            }

            if (!Schema::hasColumn('wwph_jobs', 'requirements')) {
                $table->text('requirements')->nullable();
            }

            if (!Schema::hasColumn('wwph_jobs', 'work_type')) {
                $table->unsignedBigInteger('work_type')->default(1);
            }

            if (!Schema::hasColumn('wwph_jobs', 'job_type')) {
                $table->unsignedBigInteger('job_type')->default(1);
            }

            if (!Schema::hasColumn('wwph_jobs', 'category')) {
                $table->unsignedBigInteger('category')->default(1);
            }

            if (!Schema::hasColumn('wwph_jobs', 'salary')) {
                $table->string('salary')->default('Monthly');
            }

            if (!Schema::hasColumn('wwph_jobs', 'budget')) {
                $table->string('budget')->default('0');
            }

            if (!Schema::hasColumn('wwph_jobs', 'experience')) {
                $table->string('experience')->default('');
            }

            if (!Schema::hasColumn('wwph_jobs', 'job_cover')) {
                $table->string('job_cover')->default('');
            }

            if (!Schema::hasColumn('wwph_jobs', 'skills')) {
                $table->text('skills')->nullable();
            }

            if (!Schema::hasColumn('wwph_jobs', 'city')) {
                $table->string('city')->default('');
            }

            if (!Schema::hasColumn('wwph_jobs', 'state')) {
                $table->string('state')->default('');
            }

            if (!Schema::hasColumn('wwph_jobs', 'country')) {
                $table->string('country')->default('');
            }

            if (!Schema::hasColumn('wwph_jobs', 'naration')) {
                $table->text('naration')->nullable();
            }

            if (!Schema::hasColumn('wwph_jobs', 'status')) {
                $table->string('status')->default('active');
            }
        });
    }

    public function down()
    {
        Schema::table('wwph_jobs', function (Blueprint $table) {
            // Optional: drop columns that were added if needed
            $columns = [
                'title','description','requirements','work_type','job_type','category',
                'salary','budget','experience','job_cover','skills','city','state','country','naration','status'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('wwph_jobs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
