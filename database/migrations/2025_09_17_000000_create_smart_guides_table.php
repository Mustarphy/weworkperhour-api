<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->string('guide_id', 120); // e.g. "va-beginner"
            $table->json('selections');      // full assessment selections
            $table->string('version')->nullable(); // optional for schema versioning
            $table->timestamps();

            $table->index('guide_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_guides');
    }
};
