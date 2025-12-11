<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // 1. DROP THE EXISTING COMPOSITE INDEX FIRST
            $table->dropIndex(['tokenable_type', 'tokenable_id']);

            // 2. CHANGE THE COLUMN TYPE
            // Requires doctrine/dbal
            $table->string('tokenable_id', 255)->change();

            // 3. RECREATE THE COMPOSITE INDEX
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Revert changes in the down method:
            $table->dropIndex(['tokenable_type', 'tokenable_id']);
            $table->unsignedBigInteger('tokenable_id')->change();
            $table->index(['tokenable_type', 'tokenable_id']);
        });
    }
};
