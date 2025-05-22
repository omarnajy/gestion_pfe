<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjectsTableForSupervisorProposals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            // Rendre student_id nullable
            $table->unsignedBigInteger('student_id')->nullable()->change();
            
            // Ajouter is_proposed_by_supervisor s'il n'existe pas déjà
            if (!Schema::hasColumn('projects', 'is_proposed_by_supervisor')) {
                $table->boolean('is_proposed_by_supervisor')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            // Remettre student_id non nullable (attention: ceci pourrait causer des erreurs si des données existent)
            $table->unsignedBigInteger('student_id')->nullable(false)->change();
            
            // Supprimer is_proposed_by_supervisor si vous le souhaitez
            if (Schema::hasColumn('projects', 'is_proposed_by_supervisor')) {
                $table->dropColumn('is_proposed_by_supervisor');
            }
        });
    }
}