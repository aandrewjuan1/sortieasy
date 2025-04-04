<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'sku',
        'price',
        'cost',
        'quantity_in_stock',
        'reorder_threshold',
        'safety_stock',
        'last_restocked',
        'supplier_id',
    ];

    protected $casts = [
        'last_restocked' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function logistics()
    {
        return $this->hasMany(Logistic::class);
    }
}
