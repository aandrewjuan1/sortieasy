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
    public function scopeFuture($query)
    {
        return $query->where('forecast_date', '>=', now());
    }

    /**
     * Scope for forecasts of a specific product.
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeOfProduct($query, $productId)
    {
        // If $productId is not an empty string, apply the filter
        if (!empty($productId)) {
            return $query->where('product_id', $productId);
        }

        // If $productId is an empty string (all products), don't apply the filter
        return $query;
    }

    /**
     * Scope for forecasts within a date range.
     */
    public function scopeDateRange($query, $dateFilter)
    {
        // Applying date range filter based on different types of filters
        switch ($dateFilter) {
            case 'last_7_days':
                return $query->where('forecast_date', '>=', now()->subDays(7));
            case 'last_30_days':
                return $query->where('forecast_date', '>=', now()->subDays(30));
            case 'this_month':
                return $query->whereMonth('forecast_date', now()->month)
                             ->whereYear('forecast_date', now()->year);
            case 'next_month':
                return $query->whereMonth('forecast_date', now()->addMonth()->month)
                             ->whereYear('forecast_date', now()->addMonth()->year);
            default:
                return $query;
        }
    }

    public function scopeDateFilter($query, $dateFilter)
    {
        if ($dateFilter) {
            // Ensure we handle 'today' properly by stripping time from the forecast_date
            if ($dateFilter == 'today') {
                return $query->whereDate('forecast_date', now()->toDateString()); // Compares only the date part, ignoring time
            }

            // Handle 'this_week' filter (current week, from Sunday to Saturday)
            elseif ($dateFilter == 'this_week') {
                return $query->whereBetween('forecast_date', [
                    now()->startOfWeek(), // Start of the week
                    now()->endOfWeek(), // End of the week
                ]);
            }

            // Handle 'last_7_days' filter (last 7 days)
            elseif ($dateFilter == 'last_7_days') {
                return $query->where('forecast_date', '>=', now()->subDays(7));
            }

            // Handle 'last_30_days' filter (last 30 days)
            elseif ($dateFilter == 'last_30_days') {
                return $query->where('forecast_date', '>=', now()->subDays(30));
            }

            // Handle 'this_month' filter (current month)
            elseif ($dateFilter == 'this_month') {
                return $query->whereMonth('forecast_date', now()->month)
                            ->whereYear('forecast_date', now()->year);
            }

            // Handle 'next_month' filter (next month)
            elseif ($dateFilter == 'next_month') {
                return $query->whereMonth('forecast_date', now()->addMonth()->month)
                            ->whereYear('forecast_date', now()->addMonth()->year);
            }
        }

        // Return query unmodified if no dateFilter
        return $query;
    }

    /**
     * Search scope to filter by product name or forecast data.
     */
    public function scopeSearch($query, $search)
    {
        return $query->whereHas('product', function ($q) use ($search) {
            $q->where('name', 'like', "%$search%");
        });
    }
}
