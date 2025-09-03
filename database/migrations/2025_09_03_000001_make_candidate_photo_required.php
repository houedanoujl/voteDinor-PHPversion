<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill existing NULL values to empty string to satisfy NOT NULL constraint
        DB::table('candidates')->whereNull('photo_url')->update(['photo_url' => '']);

        // SQLite uses an internal temp table when rebuilding schemas for column changes.
        // If a previous attempt failed, the temp table may persist; drop it proactively.
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP TABLE IF EXISTS "__temp__candidates"');
        }

        Schema::table('candidates', function (Blueprint $table) {
            $table->string('photo_url')->default('')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DROP TABLE IF EXISTS "__temp__candidates"');
        }
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('photo_url')->nullable()->default(null)->change();
        });
    }
};


