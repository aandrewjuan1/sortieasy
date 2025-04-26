<?php

namespace App\Models;

use App\Observers\ProductObserver;
use App\Traits\Auditable;
use App\Enums\InventoryStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([ProductObserver::class])]
class Product extends Model
{
    use HasFactory;
    use Auditable;

    protected $guarded = [
    ];

    protected $casts = [
        'last_restocked' => 'date',
        'inventory_status' => InventoryStatus::class
    ];

    public function scopeSearch(Builder $query, $search)
    {
        if (!empty($search)) {
            return $query->where(function ($query) use ($search) {
                // Search in product attributes like name, category, SKU, description
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");

                // Check if the search term is a number and search in quantity_in_stock
                if (is_numeric($search)) {
                    $query->orWhere('quantity_in_stock', '=', $search);
                }
            });
        }

        return $query;
    }

    public function scopeStatusFilter($query, $status)
    {
        if ($status) {
            return $query->where('inventory_status', $status);
        }
        return $query;
    }

    public function scopeCategoryFilter(Builder $query, ?string $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
    }

    public function scopeStockFilter(Builder $query, ?string $stockStatus)
    {
        return match ($stockStatus) {
            'low' => $query->where('quantity_in_stock', '<=', DB::raw('reorder_threshold'))
                           ->where('quantity_in_stock', '>', DB::raw('safety_stock')),

            'critical' => $query->where('quantity_in_stock', '<=', DB::raw('safety_stock')),

            default => $query,
        };
    }

    // In your Product model
    public function scopeOrderByField($query, $field, $direction)
    {
        // Fallback to created_at if the requested field isn't valid
        $validFields = ['name', 'price', 'quantity_in_stock', 'last_restocked', 'created_at'];
        $field = in_array($field, $validFields) ? $field : 'created_at';

        return $query->orderBy($field, $direction);
    }

    public function scopeSupplierFilter($query, $supplierName)
    {
        if ($supplierName) {
            if ($supplierName === 'None') {
                // Filter for products with no supplier (null supplier_id)
                return $query->whereNull('supplier_id');
            }

            // Otherwise, filter products by supplier name
            return $query->whereHas('supplier', function($query) use ($supplierName) {
                $query->where('name', 'like', '%' . $supplierName . '%');
            });
        }

        return $query;
    }

    public function scopeWithSupplier($query)
    {
        return $query->with('supplier:id,name');
    }


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    public function restockingRecommendations()
    {
        return $this->hasMany(RestockingRecommendation::class);
    }

    // Define the relationship with AnomalyDetectionResult
    public function anomalyDetectionResults()
    {
        return $this->hasMany(AnomalyDetectionResult::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function logistics()
    {
        return $this->hasMany(Logistic::class);
    }
}
