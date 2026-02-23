<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimHistory extends Model
{
    protected $fillable = ['claim_id', 'actor_user_id', 'old_status', 'new_status', 'notes', 'acted_at'];
    protected $casts = ['acted_at' => 'datetime'];

    public function claim()
    {
        return $this->belongsTo(WarrantyClaim::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
