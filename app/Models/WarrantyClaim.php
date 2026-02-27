<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WarrantyClaim extends Model
{
    protected $fillable = [
        'product_id', 
        'claimed_by_user_id', 
        'distributor_id', 
        'handled_by', 
        'approved_by', 
        'claim_number', 
        'status', 
        'complaint_type', 
        'complaint_description', 
        'photo_evidence', 
        'defect_notes', 
        'resolution_notes', 
        'submitted_at', 
        'reviewed_at', 
        'approved_at', 
        'resolved_at',
        'replaced_at',
        'replacement_product_id',
        'motor_type',
        'has_modification',
        'modification_types',
        'whatsapp_number',
        'purchase_type',
        'purchase_date',
        'battery_issue_date'
    ];
    
    protected $casts = [
        'submitted_at' => 'datetime', 
        'reviewed_at' => 'datetime', 
        'approved_at' => 'datetime', 
        'resolved_at' => 'datetime',
        'replaced_at' => 'datetime',
        'has_modification' => 'boolean',
        'modification_types' => 'array',
        'purchase_date' => 'date',
        'battery_issue_date' => 'date'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($claim) {
            if (!$claim->claim_number) {
                $claim->claim_number = 'CLM-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function replacementProduct()
    {
        return $this->belongsTo(Product::class, 'replacement_product_id');
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by_user_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function histories()
    {
        return $this->hasMany(ClaimHistory::class, 'claim_id');
    }
}
