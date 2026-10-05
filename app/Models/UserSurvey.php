<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSurvey extends Model
{
    protected $fillable = ['user_id', 'q1_riding_experience', 'q2_battery_lifespan', 'q3_replacement_reason'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
