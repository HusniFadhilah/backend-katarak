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
        Schema::create('eye_disorder_examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eye_examination_id')->constrained('eye_examinations')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('eye_disorder_id')->constrained('eye_disorders')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eye_disorder_examinations');
    }
};
