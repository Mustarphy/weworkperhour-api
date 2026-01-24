<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployerPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('employer_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employer_id');
            $table->unsignedBigInteger('candidate_id');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['escrow', 'milestone'])->default('escrow');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('reference')->unique();
            $table->string('wallet_token');
            $table->enum('payment_method', ['paystack', 'bank_transfer'])->default('paystack');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('employer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('candidate_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('status');
            $table->index('reference');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employer_payments');
    }
}