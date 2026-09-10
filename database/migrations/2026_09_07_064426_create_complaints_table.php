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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            $table->string('complaint_number')->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // What citizen selected
            $table->foreignId('user_category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            // What AI finally decided
            $table->foreignId('ai_category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Automatically detected using GPS
            $table->foreignId('ward_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // GPS
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('location_accuracy', 10, 2)->nullable();

            $table->text('description');

            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'critical'
            ])->default('medium');

            $table->enum('status', [
                'submitted',
                'ai_processing',
                'assigned',
                'in_progress',
                'resolved',
                'verification_pending',
                'closed',
                'reopened',
                'rejected'
            ])->default('submitted');

            // AI decision
            $table->decimal('ai_confidence', 5, 2)->nullable();
            $table->text('ai_decision_reason')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
