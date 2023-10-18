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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('created_by')->default(null)->nullable()->constrained('users');
            $table->foreignId('modificated_by')->default(null)->nullable()->constrained('users');
            $table->string('name');
            $table->string('ktp')->default(null)->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->date('birth_date')->default(null)->nullable();
            $table->string('birth_place')->default(null)->nullable();
            $table->string('address')->default(null)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
