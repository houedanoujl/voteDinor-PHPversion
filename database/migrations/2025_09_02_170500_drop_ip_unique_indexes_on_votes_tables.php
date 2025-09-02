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
        // Supprimer l'index unique basé sur IP pour permettre plusieurs votes depuis la même IP
        Schema::table('votes', function (Blueprint $table) {
            // Index créé dans 2025_08_28_104005_create_votes_table.php
            $table->dropUnique('unique_ip_candidate_daily_vote');
        });

        // Par cohérence, supprimer aussi la contrainte équivalente dans vote_limits si elle existe
        if (Schema::hasTable('vote_limits')) {
            Schema::table('vote_limits', function (Blueprint $table) {
                // Index créé dans 2025_08_28_104010_create_vote_limits_table.php
                $table->dropUnique('unique_ip_candidate_limit');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->unique(['candidate_id', 'ip_address', 'vote_date'], 'unique_ip_candidate_daily_vote');
        });

        if (Schema::hasTable('vote_limits')) {
            Schema::table('vote_limits', function (Blueprint $table) {
                $table->unique(['candidate_id', 'ip_address', 'vote_date'], 'unique_ip_candidate_limit');
            });
        }
    }
};


