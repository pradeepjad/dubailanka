<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_entities', fn (Blueprint $table) => $table->string('email')->nullable()->change());
        Schema::table('stores', fn (Blueprint $table) => $table->string('email')->nullable()->change());
    }

    public function down(): void
    {
        Schema::table('business_entities', fn (Blueprint $table) => $table->string('email')->nullable(false)->change());
        Schema::table('stores', fn (Blueprint $table) => $table->string('email')->nullable(false)->change());
    }
};
