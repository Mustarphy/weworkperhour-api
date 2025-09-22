<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_guides_content', function (Blueprint $table) {
            $table->id();
            $table->string('guide_id', 120)->unique(); // e.g., "va-beginner"
            $table->string('title');
            $table->string('category'); // VA or Editor
            $table->string('level');    // Beginner or Advanced
            $table->text('roleOverview')->nullable();
            $table->json('clientAcquisition')->nullable();
            $table->json('pricingPackages')->nullable();
            $table->json('toolsTemplates')->nullable();
            $table->json('serviceStandards')->nullable();
            $table->json('quickWins')->nullable();
            $table->json('aiAutomationTips')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_guides_content');
    }
};
