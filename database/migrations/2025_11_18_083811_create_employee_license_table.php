<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_license', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('license_id')->constrained()->onDelete('cascade');
            $table->string('assigned_date');
            $table->string('expiry_date')->nullable();
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->timestamps();

            // Prevent duplicate assignments
            $table->unique(['employee_id', 'license_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_license');
    }
};
