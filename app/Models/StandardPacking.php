<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StandardPacking extends Model
{
    protected $fillable = ['project_id', 'variant', 'packing_code', 'quantity', 'created_by', 'packed_at'];
    protected $casts = ['packed_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($packing) {
            if (!$packing->packing_code) {
                $packing->packing_code = 'PACK-' . strtoupper(Str::random(10));
            }
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
