<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('incident_id')
                ->nullable()
                ->after('id')
                ->constrained('civic_incidents')
                ->nullOnDelete();

            $table->index('incident_id');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['incident_id']);
            $table->dropIndex(['incident_id']);
            $table->dropColumn('incident_id');
        });
    }
};