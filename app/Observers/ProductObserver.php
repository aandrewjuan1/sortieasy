<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use App\Models\RestockingRecommendation;

class ProductObserver
{
    private const SAFETY_DAYS = 5;  // Safety days to cover stock buffer
    private const FORECAST_DAYS = 30;
    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        // Handle the restocking recommendations when the product is updated
        $this->updateRestockingRecommendations($product);

        // Check if the restocking recommendation needs to be deleted
        $this->deleteRestockingRecommendationIfFulfilled($product);
        Cache::forget('restocking_recommendations:page:1:per_page:10:sort:name:dir:ASC:search:');
    }

    /**
     * Handle the Product "created" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        // Handle the restocking recommendations when the product is created
        $this->updateRestockingRecommendations($product);
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        // Optionally, delete or update restocking recommendations when a product is deleted
        $product->restockingRecommendation()->delete();
    }

    /**
     * Custom function to update restocking recommendations
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    private function updateRestockingRecommendations(Product $product)
    {
        $recommendation = $product->restockingRecommendation;

        if (!$recommendation)
        {
            return;
        }

        // Calculate average daily demand based on the total forecasted demand (you can adjust this logic as needed)
        $avgDailyDemand = $recommendation->total_forecasted_demand / $this::FORECAST_DAYS;

        // Calculate reorder threshold (Safety stock + forecast demand for the forecast days)
        $reorderThreshold = $avgDailyDemand * ($this::SAFETY_DAYS + $this::FORECAST_DAYS);

        // Calculate reorder quantity
        $reorderQuantity = max(0, $reorderThreshold - $product->quantity_in_stock);

        // Calculate projected stock
        $recommendation->projected_stock = $product->quantity_in_stock - $recommendation->total_forecasted_demand;

        // Update the recommendation with the calculated values
        $recommendation->quantity_in_stock = $product->quantity_in_stock;
        $recommendation->reorder_quantity = $reorderQuantity;

        // Save the updated or new restocking recommendation
        $recommendation->save();
    }



    /**
     * Custom function to delete the restocking recommendation if restocked
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    private function deleteRestockingRecommendationIfFulfilled(Product $product)
    {
        // Check if the product's stock is sufficient
        $recommendation = $product->restockingRecommendation()->first();

        if ($recommendation && $product->quantity_in_stock >= ($recommendation->total_forecasted_demand + $recommendation->reorder_quantity)) {
            // Delete the restocking recommendation if restocking needs are fulfilled
            $recommendation->delete();
        }
    }
}
