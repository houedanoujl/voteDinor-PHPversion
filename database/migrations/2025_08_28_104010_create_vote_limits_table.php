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
        Schema::create('vote_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('supabase_user_id')->nullable();
            $table->ipAddress('ip_address');
            $table->date('vote_date');
            $table->integer('vote_count')->default(1);
            $table->timestamps();
            
            // Index unique pour le rate limiting
            $table->unique(['candidate_id', 'user_id', 'vote_date'], 'unique_user_candidate_limit');
            $table->unique(['candidate_id', 'ip_address', 'vote_date'], 'unique_ip_candidate_limit');
            $table->index('vote_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_limits');
    }
};
