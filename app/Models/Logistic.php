<?php

namespace App\Models;

use App\Enums\LogisticStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Logistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'delivery_date',
        'status',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'status' => LogisticStatus::class,
    ];

    public function scopeSearch(Builder $query, string $search = null): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->whereHas('product', function($productQuery) use ($search) {
                $productQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            })
            ->orWhere('status', 'like', '%' . $search . '%');
        });
    }

    public function scopeOfStatus(Builder $query, string $status = null): Builder
    {
        if (!$status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
