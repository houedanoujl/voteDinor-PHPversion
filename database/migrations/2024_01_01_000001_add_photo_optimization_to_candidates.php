<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->timestamp('photo_optimized_at')->nullable()->after('photo_url');
            $table->enum('photo_optimization_status', ['pending', 'processing', 'completed', 'failed'])
                  ->default('pending')
                  ->after('photo_optimized_at');
            $table->text('photo_optimization_error')->nullable()->after('photo_optimization_status');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn([
                'photo_optimized_at',
                'photo_optimization_status', 
                'photo_optimization_error'
            ]);
        });
    }
};