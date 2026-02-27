<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retail extends Model
{
    protected $fillable = [
        'distributor_id',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'status',
        'pin',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
