<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('store_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by_user_id')->constrained('users');
            $table->string('status', 30)->default('pending');
            $table->string('proposed_name', 150)->nullable();
            $table->string('proposed_slug', 120)->nullable();
            $table->char('proposed_country_code', 2)->nullable();
            $table->string('proposed_city', 120)->nullable();
            $table->text('proposed_address')->nullable();
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('review_started_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['store_id', 'status']);
            $table->index(['proposed_slug', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_change_requests');
    }
};
