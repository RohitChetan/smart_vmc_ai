<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resolution_proofs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('complaint_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('incident_id')
                ->nullable()
                ->constrained('civic_incidents')
                ->nullOnDelete();

            $table->foreignId('submitted_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('type', [
                'image',
                'video',
            ]);

            $table->string('file_path');

            $table->string('mime_type')->nullable();

            $table->unsignedBigInteger('file_size')->nullable();

            /*
            |--------------------------------------------------------------------------
            | GPS captured at resolution
            |--------------------------------------------------------------------------
            */

            $table->decimal('latitude', 10, 7);

            $table->decimal('longitude', 10, 7);

            $table->decimal('location_accuracy', 8, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Capture / verification
            |--------------------------------------------------------------------------
            */

            $table->timestamp('captured_at')->nullable();

            $table->enum('ai_status', [
                'pending',
                'processing',
                'verified',
                'rejected',
            ])->default('pending');

            $table->decimal('ai_confidence', 5, 4)->nullable();

            $table->json('ai_result')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index([
                'complaint_id',
                'ai_status',
            ]);

            $table->index([
                'incident_id',
                'ai_status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resolution_proofs');
    }
};