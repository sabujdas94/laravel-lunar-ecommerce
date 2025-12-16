<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, apply column changes (without adding the unique index)
        Schema::table('users', function (Blueprint $table) {
            // Make email nullable and ensure phone column is string
            $table->string('email')->nullable()->change();
            $table->string('phone')->change();
        });

        // Then, add the unique index only if it doesn't already exist
        $index = DB::select("SHOW INDEX FROM `users` WHERE Key_name = ?", ['users_phone_unique']);
        if (empty($index)) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('phone');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop unique index only if it exists, then revert column changes
        $index = DB::select("SHOW INDEX FROM `users` WHERE Key_name = ?", ['users_phone_unique']);
        if (!empty($index)) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['phone']);
            });
        }

        Schema::table('users', function (Blueprint $table) {
            // Revert changes
            $table->string('email')->nullable(false)->change();
            $table->string('phone')->change();
        });
    }
};
