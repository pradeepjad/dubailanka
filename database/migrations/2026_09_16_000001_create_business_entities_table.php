<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_entities', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40);
            $table->string('legal_name', 150);
            $table->string('trading_name', 150)->nullable();
            $table->string('registration_number', 100)->nullable();
            $table->char('country_code', 2);
            $table->string('address', 500)->nullable();
            $table->string('phone', 30);
            $table->string('whatsapp', 30)->nullable();
            $table->string('email');
            $table->string('status', 30)->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index('country_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_entities');
    }
};
