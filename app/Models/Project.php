<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = ['created_by_user_id', 'code', 'project_code', 'qr_code', 'name', 'description', 'logo', 'status', 'warranty_duration', 'standard_packing_quantity', 'use_variants', 'variants', 'packing_format', 'settings'];
    protected $casts = ['settings' => 'array', 'variants' => 'array', 'use_variants' => 'boolean'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->code)) {
                $project->code = 'PRJ-' . strtoupper(Str::random(8));
            }
            if (empty($project->qr_code)) {
                $project->qr_code = 'QR-PRJ-' . Str::uuid();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_users');
    }

    public function distributors()
    {
        return $this->hasMany(Distributor::class);
    }

    public function categories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function standardPackings()
    {
        return $this->hasMany(StandardPacking::class);
    }
}
