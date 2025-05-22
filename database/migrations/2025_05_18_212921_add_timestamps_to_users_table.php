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
        // Vérifier si les colonnes n'existent pas déjà
        if (!Schema::hasColumn('users', 'created_at') || !Schema::hasColumn('users', 'updated_at')) {
            Schema::table('users', function (Blueprint $table) {
                // Ajouter les colonnes de timestamps si elles n'existent pas
                if (!Schema::hasColumn('users', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('users', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
            
            // Mettre à jour les enregistrements existants
            DB::table('users')->whereNull('created_at')->update(['created_at' => now()]);
            DB::table('users')->whereNull('updated_at')->update(['updated_at' => now()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire ici, car nous ne voulons pas supprimer ces colonnes
    }
};