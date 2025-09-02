<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Empêche plus d'une candidature par utilisateur (user_id non nul)
        Schema::table('candidates', function (Blueprint $table) {
            $table->unique('user_id', 'candidates_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropUnique('candidates_user_id_unique');
        });
    }
};


