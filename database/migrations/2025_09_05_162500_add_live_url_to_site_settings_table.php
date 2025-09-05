<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('live_url')->nullable()->after('uploads_enabled');
        });

        // Pré-remplir avec le lien fourni si une ligne existe déjà
        $existing = DB::table('site_settings')->first();
        if ($existing) {
            DB::table('site_settings')->update(['live_url' => 'https://www.facebook.com/share/v/172P5EMjL2/']);
        }
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('live_url');
        });
    }
};
