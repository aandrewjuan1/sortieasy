<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockingRecommendation extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'total_forecasted_demand',
        'quantity_in_stock',
        'projected_stock',
        'reorder_quantity',
    ];

    protected $casts = [
        'total_forecasted_demand' => 'float',
        'quantity_in_stock' => 'integer',
        'projected_stock' => 'float',
        'reorder_quantity' => 'float',
    ];

    /**
     * Relationship to the Product model.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
