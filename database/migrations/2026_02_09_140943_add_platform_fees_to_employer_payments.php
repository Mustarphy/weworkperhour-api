<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlatformFeesToEmployerPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employer_payments', function (Blueprint $table) {
            // Employer side
            $table->decimal('employer_pays_total', 15, 2)->after('amount')->comment('Total employer pays (base + fee + VAT)');
            $table->decimal('platform_fee', 15, 2)->after('employer_pays_total')->comment('5% platform fee from employer');
            
            // Freelancer side
            $table->decimal('freelancer_receives', 15, 2)->after('platform_fee')->comment('Amount freelancer receives (base - commission - VAT)');
            $table->decimal('platform_commission', 15, 2)->after('freelancer_receives')->comment('20% platform commission from freelancer');
            
            // VAT (combined from both sides)
            $table->decimal('platform_vat', 15, 2)->after('platform_commission')->comment('Total VAT on platform earnings');
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
            $table->dropColumn([
                'employer_pays_total',
                'platform_fee',
                'freelancer_receives',
                'platform_commission',
                'platform_vat',
            ]);
        });
    }
}
