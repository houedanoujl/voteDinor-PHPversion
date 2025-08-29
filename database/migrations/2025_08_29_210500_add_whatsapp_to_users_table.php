<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Allow null to not break existing rows; uniqueness still enforced for non-null values
            if (!Schema::hasColumn('users', 'whatsapp')) {
                $table->string('whatsapp', 20)->nullable()->unique()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'whatsapp')) {
                $table->dropUnique(['whatsapp']);
                $table->dropColumn('whatsapp');
            }
        });
    }
};


