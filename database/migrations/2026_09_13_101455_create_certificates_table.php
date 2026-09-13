<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('complaint_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('certificate_number')->unique();

            $table->string('title')->default('Jagruk Nagrik Certificate');

            $table->timestamp('issued_at');

            $table->timestamps();

            $table->unique(
                ['user_id', 'complaint_id'],
                'certificates_user_complaint_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};