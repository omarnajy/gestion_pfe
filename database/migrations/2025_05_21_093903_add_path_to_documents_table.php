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
        Schema::table('documents', function (Blueprint $table) {
            // Ajouter la colonne path s'il n'existe pas
            if (!Schema::hasColumn('documents', 'path')) {
                $table->string('path')->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Supprimer la colonne si elle existe
            if (Schema::hasColumn('documents', 'path')) {
                $table->dropColumn('path');
            }
        });
    }
};