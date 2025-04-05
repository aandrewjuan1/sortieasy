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
              ->orWhere('contact_phone', 'like', "%{$search}%");
        });
    }

    public function latestDelivery()
    {
        return $this->hasOneThrough(
            Logistic::class,
            Product::class,
            'supplier_id', // Foreign key on products table
            'product_id',  // Foreign key on logistics table
            'id',         // Local key on suppliers table
            'id'          // Local key on products table
        )->where('status', 'delivered')
        ->latest('delivery_date')
        ->withDefault(); // This provides a default null object if no delivery exists
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
