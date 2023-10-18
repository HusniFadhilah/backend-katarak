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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('created_by')->default(null)->nullable()->constrained('users');
            $table->foreignId('modificated_by')->default(null)->nullable()->constrained('users');
            $table->string('name');
            $table->string('phone_number')->default(null)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->default(null)->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->boolean('is_active')->comment('0-false,1-true')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
