<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTraceLog extends Model
{
    protected $fillable = ['product_id', 'user_id', 'scanned_by', 'action', 'event_type', 'location', 'notes', 'latitude', 'longitude', 'scanned_at'];
    protected $casts = ['scanned_at' => 'datetime'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
