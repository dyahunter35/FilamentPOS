<?php

use App\Models\Branch;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Branch::class)->index();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('number', 32)->unique();
            $table->decimal('total_price', 12, 2)->nullable();
            $table->enum('status', ['new', 'processing', 'shipped', 'delivered', 'cancelled'])->default('new');
            $table->string('currency');

            $table->double('total')->default(0);
            $table->double('discount')->default(0);
            $table->double('shipping')->default(0);
            $table->double('install')->default(0);
            $table->double('paid')->default(0);

            $table->string('shipping_method')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('caused_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
