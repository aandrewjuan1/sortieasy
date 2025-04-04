<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TransactionType;

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

    // app/Models/Transaction.php
    public function getTypeNameAttribute()
    {
        return $this->type->value; // Get the human-readable name for the type
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
