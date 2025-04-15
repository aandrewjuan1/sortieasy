<?php

namespace App\Models;

use App\Enums\SaleChannel;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;
    use Auditable;

    protected $guarded = [
    ];

    protected $casts = [
        'channel' => SaleChannel::class, // Casting 'channel' to SaleChannel enum
        'sale_date' => 'datetime',
    ];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('sku', 'like', "%$search%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        });
    }

    public function scopeFilterByChannel(Builder $query, ?string $channel): Builder
    {
        return $channel ? $query->where('channel', $channel) : $query;
    }

    public function scopeFilterByDate(Builder $query, ?string $filter): Builder
    {
        return match ($filter) {
            'today' => $query->whereDate('sale_date', today()),
            'yesterday' => $query->whereDate('sale_date', today()->subDay()),
            'week' => $query->whereBetween('sale_date', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereBetween('sale_date', [now()->startOfMonth(), now()->endOfMonth()]),
            'year' => $query->whereBetween('sale_date', [now()->startOfYear(), now()->endOfYear()]),
            default => $query,
        };
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
