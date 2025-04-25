<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\RestockingRecommendation;

class ProductObserver
{
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
        $product->restockingRecommendations()->delete();
    }

    /**
     * Custom function to update restocking recommendations
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    private function updateRestockingRecommendations(Product $product)
    {
        // Check if restocking recommendation exists for this product
        $recommendation = $product->restockingRecommendations()->first();

        // If no existing recommendation, create one
        if (!$recommendation) {
            $recommendation = new RestockingRecommendation();
            $recommendation->product_id = $product->id;
        }

        // Get the product details
        $quantity_in_stock = $product->quantity_in_stock;
        $reorder_threshold = $product->reorder_threshold;
        $safety_stock = $product->safety_stock;

        // Ensure forecasted_demand is available, otherwise provide a default value
        $forecasted_demand = $product->forecasted_demand ?? 0;  // Default to 0 if not set

        // Calculate the restocking values
        $recommendation->total_forecasted_demand = $forecasted_demand;
        $recommendation->quantity_in_stock = $quantity_in_stock;
        $recommendation->projected_stock = $quantity_in_stock + $recommendation->total_forecasted_demand;
        $recommendation->reorder_quantity = max(0, $recommendation->projected_stock - $quantity_in_stock);

        // Adjust reorder quantity based on reorder threshold
        if ($quantity_in_stock <= $reorder_threshold) {
            // Reorder if stock is below the threshold
            $recommendation->reorder_quantity = max($recommendation->reorder_quantity, $safety_stock);
        } else {
            // Otherwise, ensure reorder quantity is positive but minimal
            $recommendation->reorder_quantity = max(0, $recommendation->reorder_quantity);
        }

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
        $recommendation = $product->restockingRecommendations()->first();

        if ($recommendation && $product->quantity_in_stock >= ($recommendation->total_forecasted_demand + $recommendation->reorder_quantity)) {
            // Delete the restocking recommendation if restocking needs are fulfilled
            $recommendation->delete();
        }
    }
}
