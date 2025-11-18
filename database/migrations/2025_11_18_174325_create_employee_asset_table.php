<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_asset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('assigned_date');
            $table->string('return_date')->nullable();
            $table->enum('assignment_status', ['active', 'returned', 'damaged'])->default('active');
            $table->text('assignment_notes')->nullable();
            $table->timestamps();

            // Prevent duplicate active assignments
            $table->unique(['asset_id', 'employee_id', 'assignment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_asset');
    }
};
