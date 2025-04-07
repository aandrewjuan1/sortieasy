<?php

use App\Enums\SaleChannel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // Link to product
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Who made the sale (optional)
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // Price per item at time of sale
            $table->decimal('total_price', 10, 2); // quantity * unit_price
            $table->enum('channel', SaleChannel::getValues()) // Use values from SaleChannel enum
                ->default(SaleChannel::InStore->value); // Default to 'in_store'
            $table->date('sale_date');
            $table->timestamps();

            $table->index(['sale_date', 'product_id']); // For forecast models & reporting
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
