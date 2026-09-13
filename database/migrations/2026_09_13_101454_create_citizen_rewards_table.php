<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citizen_rewards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedInteger('total_points')->default(0);

            $table->string('level')->default('Citizen');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizen_rewards');
    }
};