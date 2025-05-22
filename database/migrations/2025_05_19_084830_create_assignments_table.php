<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // Dans votre migration pour la table assignments
Schema::create('assignments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('users');
    $table->foreignId('supervisor_id')->constrained('users');
    $table->foreignId('project_id')->nullable()->constrained('projects');
    $table->timestamps();
    
    // Garantir qu'un étudiant n'est affecté qu'une seule fois
    $table->unique('student_id');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
