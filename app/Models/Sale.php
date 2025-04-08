<?php

namespace App\Models;

use App\Enums\SaleChannel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'user_id', 'quantity', 'unit_price', 'total_price', 'channel', 'sale_date'
    ];

    protected $casts = [
        'channel' => SaleChannel::class, // Casting 'channel' to SaleChannel enum
        'sale_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
