<?php

use App\Enums\InventoryStatus;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('quantity_in_stock')->default(0);

            $table->integer('reorder_threshold')->default(10);
            $table->integer('suggested_reorder_threshold')->nullable();

            $table->integer('safety_stock')->default(5);
            $table->integer('suggested_safety_stock')->nullable();

            $table->date('last_restocked')->nullable();
            $table->date('last_forecast_update')->nullable();

            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('inventory_status', InventoryStatus::getValues())->nullable();

            $table->timestamps();

            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
