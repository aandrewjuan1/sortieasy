<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\AlertType;

class Alert extends Model
{
    use HasFactory;

    protected $guarded = [
    ];

    protected $casts = [
        'resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'type' => AlertType::class,
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
