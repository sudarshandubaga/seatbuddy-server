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
        Schema::table('slot_packages', function (Blueprint $table) {
            $table->boolean('is_full_day')->default(false)->after('description');
            $table->boolean('is_overnight')->default(false)->after('is_full_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slot_packages', function (Blueprint $table) {
            $table->dropColumn(['is_full_day', 'is_overnight']);
        });
    }
};
