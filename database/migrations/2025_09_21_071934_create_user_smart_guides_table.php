<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_smart_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('guide_id', 120); // corresponds to smart_guides_content.guide_id
            $table->json('selections')->nullable();
            $table->json('completed_modules')->nullable(); // track user progress
            $table->timestamps();

            $table->unique(['user_id', 'guide_id']); // one guide per user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_smart_guides');
    }
};
