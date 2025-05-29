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
        Schema::create('defenses', function (Blueprint $table) {
           $table->id();
           $table->foreignId('project_id')->constrained()->onDelete('cascade');
           $table->date('date');
           $table->time('time');
           $table->string('location');
           $table->integer('duration')->default(40); // en minutes
           $table->json('jury_members'); // [{name, role, email}]
           $table->text('notes')->nullable();
           $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defenses');
    }
};
