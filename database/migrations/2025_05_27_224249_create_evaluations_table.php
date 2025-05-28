<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->decimal('presentation_grade', 4, 2);
            $table->decimal('documentation_grade', 4, 2);
            $table->decimal('grade', 4, 2); // Note finale calculée
            $table->timestamps();
            
            // Un projet ne peut avoir qu'une évaluation par évaluateur
            $table->unique(['project_id', 'evaluator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};