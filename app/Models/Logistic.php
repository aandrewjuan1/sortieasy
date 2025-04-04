<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\LogisticStatus;

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

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
