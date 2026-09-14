<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            // Setting identification
            $table->string('key')->unique();
            $table->string('group')->default('general');

            // Value storage
            $table->longText('value')->nullable();
            $table->string('type')->default('string');

            // Admin UI
            $table->string('label')->nullable();
            $table->text('description')->nullable();

            // Visibility / status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_encrypted')->default(false);

            $table->timestamps();

            $table->index('group');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};