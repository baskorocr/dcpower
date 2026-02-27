<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['product_id', 'distributor_id', 'retail_id', 'type', 'quantity', 'document_no', 'document_path', 'notes', 'moved_at'];
    protected $casts = ['moved_at' => 'datetime'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function retail()
    {
        return $this->belongsTo(Retail::class);
    }
}
