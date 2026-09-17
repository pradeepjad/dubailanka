<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('platform_admins', function(Blueprint $table){ $table->id(); $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete(); $table->boolean('is_super_admin')->default(false); $table->string('status',20)->default('active'); $table->timestamps(); }); } public function down(): void { Schema::dropIfExists('platform_admins'); } };
