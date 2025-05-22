<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeywordsAndTechnologiesToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            // Vérifier si la colonne existe déjà pour éviter les erreurs
            if (!Schema::hasColumn('projects', 'keywords')) {
                $table->string('keywords')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('projects', 'technologies')) {
                $table->string('technologies')->nullable()->after('keywords');
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
            $table->dropColumn(['keywords', 'technologies']);
        });
    }
}