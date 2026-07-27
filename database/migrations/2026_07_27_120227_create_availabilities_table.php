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
    Schema::create('availabilities', function (Blueprint $table) {
        $table->id();
        $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
        $table->unsignedTinyInteger('jour_semaine');
        $table->time('heure_debut');
        $table->time('heure_fin');
        $table->boolean('actif')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
