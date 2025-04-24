<?php

namespace App\Models;

use App\Enums\AnomalyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnomalyDetectionResult extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'anomaly_detection_results';

    // Disable timestamps if you're manually handling them or want them to be managed automatically.
    // Laravel will automatically manage `created_at` and `updated_at` unless you disable them.
    public $timestamps = true; // Enable if you want Laravel to automatically manage timestamps

    // The attributes that are mass assignable.
    protected $fillable = [
        'transaction_id',
        'product_id',
        'anomaly_score',
        'status',
        'created_at',
        'updated_at',
    ];

    // The attributes that should be cast to native types.
    protected $casts = [
        'status' => AnomalyStatus::class, // Cast 'status' to AnomalyStatus enum
    ];

    // Define relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
