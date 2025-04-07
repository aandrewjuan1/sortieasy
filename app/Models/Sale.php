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
    ];
}
