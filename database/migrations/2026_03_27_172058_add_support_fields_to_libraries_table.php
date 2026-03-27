<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('libraries', function (Blueprint $table) {
            $table->string('support_phone')->nullable();
            $table->string('support_email')->nullable();
            $table->string('support_whatsapp')->nullable();
            $table->longText('faqs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('libraries', function (Blueprint $table) {
            $table->dropColumn(['support_phone', 'support_email', 'support_whatsapp', 'faqs']);
        });
    }
};
