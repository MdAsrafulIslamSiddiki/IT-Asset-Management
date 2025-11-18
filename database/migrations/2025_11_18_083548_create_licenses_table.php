<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_code')->unique();
            $table->string('name');
            $table->string('vendor');
            $table->string('license_key')->unique();
            $table->enum('license_type', ['per-user', 'per-device', 'site-license'])->default('per-user');
            $table->integer('total_quantity');
            $table->integer('used_quantity')->default(0);
            $table->string('purchase_date');
            $table->string('expiry_date');
            $table->decimal('cost_per_license', 10, 2);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
