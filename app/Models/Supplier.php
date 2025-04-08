<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_email',
        'contact_phone',
        'address',
    ];

    /**
     * Scope a query to search suppliers by name, email, or phone.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('contact_email', 'like', "%{$search}%")
            ->orWhere('contact_phone', 'like', "%{$search}%")
            ->orWhere('address', 'like', "%{$search}%")
            ->orWhereHas('products', function ($productQuery) use ($search) {
                $productQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
            });
        });
    }

    public function scopeOrderByField($query, $field, $direction)
    {
        // Fallback to created_at if the requested field isn't valid
        $validFields = ['name', 'created_at', 'last_delivery'];
        $field = in_array($field, $validFields) ? $field : 'created_at';

        return $query->orderBy($field, $direction);
    }



    public function latestDelivery()
    {
        return $this->hasManyThrough(Logistic::class, Product::class)
                ->where('status', 'delivered') // Only consider delivered logistics
                ->orderByDesc('delivery_date')
                ->limit(1);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
