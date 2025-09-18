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
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // e.g., 'increase', 'decrease', 'initial'
            $table->integer('quantity_change'); // The amount added or removed
            $table->integer('new_quantity'); // The stock level after the change
            $table->text('notes')->nullable(); // Optional: e.g., "Order #123", "New Shipment"
            $table->foreignId('user_id')->nullable()->constrained(); // Optional: Who made the change

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
