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
        Schema::create('eye_examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('kader_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('doctor_id')->default(null)->nullable()->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->datetime('examination_date_time');
            $table->string('right_eye_vision');
            $table->string('left_eye_vision');
            $table->string('other_eye_disorder')->default(null)->nullable();
            $table->string('other_past_medical')->default(null)->nullable();
            $table->string('latitude')->default(null)->nullable();
            $table->string('longitude')->default(null)->nullable();
            $table->enum('status', ['wait', 'abnormal', 'normal']);
            $table->text('evaluation_description')->default(null)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eye_examinations');
    }
};
