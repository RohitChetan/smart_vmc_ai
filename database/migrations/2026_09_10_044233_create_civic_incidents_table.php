<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('civic_incidents', function (Blueprint $table) {
            $table->id();

            $table->string('incident_number')->unique();

            // AI / routing
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('ward_id')
                ->nullable()
                ->constrained('wards')
                ->nullOnDelete();

            // Incident location
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('location_accuracy', 10, 2)->nullable();

            // Incident information
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // Incident lifecycle
            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'critical',
            ])->default('medium');

            $table->enum('status', [
                'open',
                'assigned',
                'in_progress',
                'verification_pending',
                'resolved',
                'closed',
                'reopened',
                'rejected',
            ])->default('open');

            // Duplicate / clustering information
            $table->unsignedInteger('report_count')->default(0);
            $table->decimal('duplicate_confidence', 5, 4)->nullable();

            // Timeline
            $table->timestamp('first_reported_at')->nullable();
            $table->timestamp('last_reported_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            // Useful indexes for map/filtering
            $table->index(['ward_id', 'status']);
            $table->index(['department_id', 'status']);
            $table->index(['priority', 'status']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('civic_incidents');
    }
};