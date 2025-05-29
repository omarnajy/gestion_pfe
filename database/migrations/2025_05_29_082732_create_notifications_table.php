<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type')->nullable(); // info, warning, success, etc.
            $table->json('data')->nullable(); 
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->boolean('read')->default(false);
            $table->timestamps();

            // Index pour la relation polymorphique
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}