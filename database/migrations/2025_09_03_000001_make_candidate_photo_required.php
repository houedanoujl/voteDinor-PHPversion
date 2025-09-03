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

        Schema::table('candidates', function (Blueprint $table) {
            $table->string('photo_url')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('photo_url')->nullable()->change();
        });
    }
};


