<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_onboarding_handovers', function (Blueprint $table) {
            $table->id();
            $table->string('ownership_mode', 20);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('owner_name', 150);
            $table->string('owner_email');
            $table->foreignId('business_entity_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('pending');
            $table->foreignId('handed_over_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['owner_email', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_onboarding_handovers');
    }
};
