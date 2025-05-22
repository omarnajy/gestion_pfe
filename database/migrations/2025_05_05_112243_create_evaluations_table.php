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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->float('technical_grade')->nullable();
            $table->float('presentation_grade')->nullable();
            $table->float('documentation_grade')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // évaluateur (superviseur)
            $table->decimal('grade', 4, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->enum('type', ['milestone', 'final'])->default('milestone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
