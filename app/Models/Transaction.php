<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'type' => TransactionType::class,
    ];

    /**
     * Scope a query to search transactions.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->whereHas('product', function($productQuery) use ($search) {
                $productQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('sku', 'like', '%' . $search . '%');
            })
            ->orWhereHas('user', function($userQuery) use ($search) {
                $userQuery->where('name', 'like', '%' . $search . '%');
            })
            ->orWhere('notes', 'like', '%' . $search . '%')
            ->orWhere('type', 'like', '%' . $search . '%');
        });
    }

    /**
     * Scope a query to filter by transaction type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        if (!$type) {
            return $query;
        }

        return $query->where('type', $type);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
