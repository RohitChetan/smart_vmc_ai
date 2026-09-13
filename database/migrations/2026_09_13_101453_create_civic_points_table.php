<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('civic_points', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('complaint_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('points')->default(10);

            $table->string('reason')->default(
                'Citizen confirmed civic issue resolution.'
            );

            $table->timestamps();

            // One successful verification can award points only once.
            $table->unique(
                ['user_id', 'complaint_id'],
                'civic_points_user_complaint_unique'
            );

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('civic_points');
    }
};