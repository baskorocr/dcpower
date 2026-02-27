<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distributor extends Model
{
    use SoftDeletes;

    protected $fillable = ['project_id', 'user_id', 'code', 'name', 'address', 'city', 'province', 'phone', 'email', 'latitude', 'longitude', 'status'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function retails()
    {
        return $this->hasMany(Retail::class);
    }
}
