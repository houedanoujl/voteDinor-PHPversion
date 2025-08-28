<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('google_id')->nullable()->after('avatar');
            $table->string('facebook_id')->nullable()->after('google_id');
            $table->boolean('is_admin')->default(false)->after('facebook_id');
            
            // Index pour améliorer les performances des recherches sociales
            $table->index('google_id');
            $table->index('facebook_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['google_id']);
            $table->dropIndex(['facebook_id']);
            $table->dropColumn(['avatar', 'google_id', 'facebook_id', 'is_admin']);
        });
    }
};
