<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMilestonesTable extends Migration
{
    public function up()
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->string('title');
            $table->decimal('percentage', 5, 2);
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'released', 'cancelled'])->default('pending');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('employer_payments')->onDelete('cascade');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('milestones');
    }
}
?>