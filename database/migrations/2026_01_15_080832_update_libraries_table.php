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
            $table->integer('no_of_tables')->default(1)->after('valid_upto');
            $table->foreignUuid('subscription_plan_id')->nullable()->after('no_of_tables')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('libraries', function (Blueprint $table) {
            // $table->dropForeign(['subscription_plan_id']);
            // sleep(2);
            $table->dropColumn('no_of_tables');
            $table->dropColumn('subscription_plan_id');
        });
    }
};
