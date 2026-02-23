<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['product_id', 'distributor_id', 'buyer_user_id', 'invoice_no', 'buyer_name', 'buyer_phone', 'buyer_email', 'sale_price', 'sale_date', 'warranty_start', 'warranty_end'];
    protected $casts = ['sale_date' => 'date', 'warranty_start' => 'date', 'warranty_end' => 'date'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function warrantyClaims()
    {
        return $this->hasMany(WarrantyClaim::class);
    }
}
