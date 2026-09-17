<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_entity_id')->constrained()->restrictOnDelete();
            $table->string('name', 150);
            $table->string('slug', 120)->unique();
            $table->text('description')->nullable();
            $table->char('country_code', 2);
            $table->string('city', 120)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('phone', 30);
            $table->string('whatsapp', 30)->nullable();
            $table->string('email');
            $table->string('website', 255)->nullable();
            $table->string('logo_path')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('business_hours', 500)->nullable();
            $table->string('status', 30)->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_entity_id', 'status']);
            $table->index(['country_code', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
