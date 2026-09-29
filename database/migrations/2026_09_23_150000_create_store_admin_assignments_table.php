<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_admin_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('platform_admin_id')->constrained('platform_admins')->cascadeOnDelete();
            $table->foreignId('assigned_by_user_id')->constrained('users');
            $table->timestamps();

            $table->index('platform_admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_admin_assignments');
    }
};
