<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('incident_id')
                ->constrained('civic_incidents')
                ->cascadeOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            // One incident can have history, but only one active assignment
            // will be enforced by application logic for now.
            $table->index(['incident_id', 'completed_at']);
            $table->index(['assigned_to', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_assignments');
    }
};