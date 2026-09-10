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
        Schema::create('complaint_ai_analyses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('complaint_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('model_name')->nullable();
            $table->string('model_version')->nullable();

            $table->string('predicted_category')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();

            // YOLO detections + other AI output
            $table->json('detections')->nullable();

            $table->json('raw_result')->nullable();

            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed'
            ])->default('pending');

            $table->text('error_message')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_a_i_analyses');
    }
};
