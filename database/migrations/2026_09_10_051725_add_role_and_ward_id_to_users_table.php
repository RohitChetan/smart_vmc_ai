<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'citizen',
                'ward_officer',
                'admin',
            ])->default('citizen')->after('email');

            $table->foreignId('ward_id')
                ->nullable()
                ->after('role')
                ->constrained('wards')
                ->nullOnDelete();

            $table->index(['role', 'ward_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['ward_id']);
            $table->dropIndex(['role', 'ward_id']);
            $table->dropColumn(['role', 'ward_id']);
        });
    }
};