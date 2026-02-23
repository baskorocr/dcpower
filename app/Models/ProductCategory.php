<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['project_id', 'name', 'description', 'warranty_months'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
