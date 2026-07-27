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
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
        $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
        $table->dateTime('date_heure_debut');
        $table->dateTime('date_heure_fin');
        $table->enum('statut', ['en_attente', 'confirme', 'annule', 'termine', 'absent'])->default('en_attente');
        $table->string('motif')->nullable();
        $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $table->softDeletes();
        $table->timestamps();

        $table->unique(['doctor_id', 'date_heure_debut']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
