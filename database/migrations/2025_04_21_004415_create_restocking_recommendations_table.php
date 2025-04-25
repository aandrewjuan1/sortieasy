<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restocking_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
            ->constrained()
            ->cascadeOnDelete();
            $table->float('total_forecasted_demand')->default(0);
            $table->integer('quantity_in_stock')->default(0);
            $table->float('projected_stock')->default(0);
            $table->float('reorder_quantity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restocking_recommendations');
    }
};
