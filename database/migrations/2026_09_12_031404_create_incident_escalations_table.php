<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_escalations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('incident_id')
                ->constrained('civic_incidents')
                ->cascadeOnDelete();

            $table->enum('level', [
                'warning',
                'breached',
                'critical',
            ]);

            $table->string('reason')->nullable();

            $table->timestamp('triggered_at');

            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            $table->index([
                'incident_id',
                'level',
            ]);

            $table->index([
                'level',
                'resolved_at',
            ]);

            $table->index('triggered_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_escalations');
    }
};