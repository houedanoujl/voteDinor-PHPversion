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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('supabase_user_id')->nullable();
            $table->ipAddress('ip_address');
            $table->string('user_agent')->nullable();
            $table->date('vote_date');
            $table->timestamps();
            
            // Index pour éviter les votes multiples
            $table->unique(['candidate_id', 'user_id', 'vote_date'], 'unique_user_candidate_daily_vote');
            $table->unique(['candidate_id', 'ip_address', 'vote_date'], 'unique_ip_candidate_daily_vote');
            $table->index(['vote_date', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
