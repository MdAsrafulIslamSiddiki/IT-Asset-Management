<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique();
            $table->string('name');
            $table->enum('type', ['Laptop', 'Phone', 'Monitor', 'Tablet', 'Printer', 'Keyboard', 'Mouse', 'Headset', 'Other'])->default('Other');
            $table->string('serial_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->string('purchase_date');
            $table->string('warranty_expiry');
            $table->decimal('value', 10, 2)->default(0);
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->enum('status', ['available', 'assigned', 'maintenance', 'retired'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
