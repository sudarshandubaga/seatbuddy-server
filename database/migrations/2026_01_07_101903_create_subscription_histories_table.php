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
        Schema::create('subscription_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('library_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('subscription_plan_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_histories');
    }
};
