<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::create('store_status_histories', function(Blueprint $table){ $table->id(); $table->foreignId('store_id')->constrained()->cascadeOnDelete(); $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete(); $table->string('from_status',30)->nullable(); $table->string('to_status',30); $table->text('note')->nullable(); $table->timestamps(); $table->index(['store_id','created_at']); }); } public function down(): void { Schema::dropIfExists('store_status_histories'); } };
