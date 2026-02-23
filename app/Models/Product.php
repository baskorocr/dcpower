<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['project_id', 'standard_packing_id', 'created_by', 'serial_number', 'image_path', 'status', 'manufactured_at', 'warranty_expires_at'];
    protected $casts = ['manufactured_at' => 'datetime', 'warranty_expires_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (!$product->serial_number) {
                $product->serial_number = 'SN-' . strtoupper(Str::random(10));
            }
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function category()
    {
        return null; // Category removed
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function traceLogs()
    {
        return $this->hasMany(ProductTraceLog::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function sale()
    {
        return $this->hasOne(Sale::class);
    }

    public function warrantyClaims()
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    public function standardPacking()
    {
        return $this->belongsTo(StandardPacking::class);
    }
}
