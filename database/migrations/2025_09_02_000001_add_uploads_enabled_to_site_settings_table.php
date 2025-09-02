<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('site_settings', 'uploads_enabled')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->boolean('uploads_enabled')->default(true)->after('applications_open');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('site_settings', 'uploads_enabled')) {
            Schema::table('site_settings', function (Blueprint $table) {
                $table->dropColumn('uploads_enabled');
            });
        }
    }
};


