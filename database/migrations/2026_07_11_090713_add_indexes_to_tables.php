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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->index('title');
            $table->index('is_active');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->index('category');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropIndex(['title']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['type']);
        });
    }
};
