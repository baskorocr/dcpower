<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['project_id', 'standard_packing_id', 'created_by', 'serial_number', 'variant', 'image_path', 'status', 'quality_checked', 'manufactured_at', 'warranty_expires_at', 'at_distributor', 'retail_stock', 'can_repair', 'repair_distributor_id', 'repair_notes', 'repair_sent_at'];
    protected $casts = ['manufactured_at' => 'datetime', 'warranty_expires_at' => 'datetime', 'repair_sent_at' => 'datetime', 'can_repair' => 'boolean', 'quality_checked' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (!$product->serial_number) {
                $product->serial_number = 'SN-' . strtoupper(Str::random(10));
            }
        });
        
        static::deleting(function ($product) {
            // Delete related warranty claims when product is deleted
            $product->warrantyClaims()->delete();
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
        return null; // Sales feature removed
    }

    public function warrantyClaims()
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    public function standardPacking()
    {
        return $this->belongsTo(StandardPacking::class);
    }

    public function repairDistributor()
    {
        return $this->belongsTo(Distributor::class, 'repair_distributor_id');
    }
}
