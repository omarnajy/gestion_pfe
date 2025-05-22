<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Ces colonnes sont utilisées dans votre code mais n'existent pas dans la base de données
        $table->string('student_id')->nullable()->after('max_students');
        $table->boolean('is_active')->default(true)->after('student_id');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'student_id',
            'is_active'
        ]);
    });
}
};
