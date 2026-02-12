<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('libraries', function (Blueprint $table) {
            $table->dropColumn('no_of_tables');
            $table->boolean('is_full_day')->default(false)->after('valid_upto');
            $table->time('opening_time')->nullable()->after('is_full_day');
            $table->time('closing_time')->nullable()->after('opening_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('libraries', function (Blueprint $table) {
            //
        });
    }
};
