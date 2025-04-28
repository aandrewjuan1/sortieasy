<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandForecast extends Model
{
    use HasFactory;

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'forecast_date',
        'predicted_quantity',
        'confidence_score',
        'model_version',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'forecast_date' => 'date',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the product associated with this forecast.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for future forecasts.
     */
    public function scopeSearch($query, ?string $searchTerm)
    {
        if (empty($searchTerm)) {
            return $query;
        }

        return $query->whereHas('product', function ($q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
            ->orWhere('sku', 'like', '%' . $searchTerm . '%');
        });
    }

    public function scopeForProduct($query, ?int $productId)
    {
        return $query->when($productId, fn($q) => $q->where('product_id', $productId));
    }

    public function scopeForDateRange($query, ?string $range)
    {
        return $query->when($range, function ($query) use ($range) {
            match ($range) {
                'today' => $query->whereDate('forecast_date', today()),
                'tomorrow' => $query->whereDate('forecast_date', today()->addDay()),
                'week' => $query->whereBetween('forecast_date', [now()->startOfWeek(), now()->endOfWeek()]),
                'month' => $query->whereBetween('forecast_date', [now()->startOfMonth(), now()->endOfMonth()]),
                'quarter' => $query->whereBetween('forecast_date', [now()->startOfQuarter(), now()->endOfQuarter()]),
                'year' => $query->whereBetween('forecast_date', [now()->startOfYear(), now()->endOfYear()]),
                'future' => $query->where('forecast_date', '>', now()),
                'past' => $query->where('forecast_date', '<=', now()),
            };
        });
    }

    public function scopeOrderByColumn($query, string $sortBy, string $sortDir)
    {
        return $query->orderBy($sortBy, $sortDir);
    }
}
