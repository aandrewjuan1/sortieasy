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

    /**
     * Scope for forecasts within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('forecast_date', [$startDate, $endDate]);
    }
}
