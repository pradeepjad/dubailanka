<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_admin_assignment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_platform_admin_id')->nullable()->constrained('platform_admins')->nullOnDelete();
            $table->foreignId('to_platform_admin_id')->constrained('platform_admins')->cascadeOnDelete();
            $table->foreignId('assigned_by_user_id')->constrained('users');
            $table->timestamp('assigned_at');
            $table->timestamps();

            $table->index(['store_id', 'assigned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_admin_assignment_histories');
    }
};
