<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_entity_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_entity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30)->default('owner');
            $table->boolean('is_primary_owner')->default(false);
            $table->timestamps();

            $table->unique(['business_entity_id', 'user_id']);
            $table->index(['user_id', 'role']);
            $table->index(['business_entity_id', 'is_primary_owner']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_entity_users');
    }
};
