<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->date('forecast_date');
            $table->decimal('predicted_quantity', 10, 2);
            $table->timestamps();

            $table->index(['product_id', 'forecast_date']);
            $table->index('forecast_date');

            $table->unique(['product_id', 'forecast_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('demand_forecasts');
    }
};
