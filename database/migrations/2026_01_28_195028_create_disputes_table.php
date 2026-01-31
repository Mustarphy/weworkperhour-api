<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up(): void
{
    Schema::create('disputes', function (Blueprint $table) {
        $table->id();
        $table->string('user_type'); // candidate | employer
        $table->string('full_name');
        $table->string('email');
        $table->string('phone');
        $table->string('dispute_category');
        $table->string('subject');
        $table->text('description');
        $table->string('priority');
        $table->json('attachments')->nullable(); // store file paths as JSON
        $table->timestamps();
    });
}
}
