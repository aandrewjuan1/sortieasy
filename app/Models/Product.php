<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'sku',
        'price',
        'cost',
        'quantity_in_stock',
        'reorder_threshold',
        'safety_stock',
        'last_restocked',
        'supplier_id',
    ];

    protected $casts = [
        'last_restocked' => 'date',
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

    public function scopeWithSupplier(Builder $query)
    {
        return $query->with('supplier');
    }

    public function scopeOrderByField(Builder $query, string $field, string $direction = 'asc')
    {
        if (in_array($field, ['name', 'price', 'quantity_in_stock', 'last_restocked'])) {
            return $query->orderBy($field, $direction);
        }
        return $query->orderBy('name', $direction);
    }


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function logistics()
    {
        return $this->hasMany(Logistic::class);
    }
}
