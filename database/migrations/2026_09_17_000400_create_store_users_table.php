<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30)->default('staff');
            $table->boolean('is_primary_owner')->default(false);
            $table->timestamps();

            $table->unique(['store_id', 'user_id']);
            $table->index(['user_id', 'role']);
            $table->index(['store_id', 'is_primary_owner']);
        });

        // Existing 03.02 draft stores receive the Primary Owner of their Business Entity.
        $stores = DB::table('stores')->select('id', 'business_entity_id')->get();
        foreach ($stores as $store) {
            $owner = DB::table('business_entity_users')
                ->where('business_entity_id', $store->business_entity_id)
                ->where('is_primary_owner', true)
                ->orderBy('id')
                ->first();

            if ($owner) {
                DB::table('store_users')->insert([
                    'store_id' => $store->id,
                    'user_id' => $owner->user_id,
                    'role' => 'owner',
                    'is_primary_owner' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('store_users');
    }
};
