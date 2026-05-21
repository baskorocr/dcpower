<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ['name', 'email', 'password', 'phone'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_users');
    }

    public function projectUsers()
    {
        return $this->hasMany(ProjectUser::class);
    }

    public function distributor()
    {
        return $this->hasOne(Distributor::class);
    }

    public function allProjects()
    {
        $projects = $this->projects;
        
        if ($this->distributor && $this->distributor->project) {
            $projects = $projects->merge([$this->distributor->project]);
        }
        
        return $projects->unique('id');
    }

    public function warrantyClaims()
    {
        return $this->hasMany(WarrantyClaim::class, 'claimed_by_user_id');
    }

    public function handledWarrantyClaims()
    {
        return $this->hasMany(WarrantyClaim::class, 'handled_by');
    }

    public function replacedWarrantyClaims()
    {
        return $this->hasMany(WarrantyClaim::class, 'replaced_by');
    }

    public function claimHistories()
    {
        return $this->hasMany(ClaimHistory::class, 'actor_user_id');
    }

    public function productTraceLogs()
    {
        return $this->hasMany(ProductTraceLog::class, 'scanned_by');
    }

    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by_user_id');
    }

    public function createdProducts()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function createdStandardPackings()
    {
        return $this->hasMany(StandardPacking::class, 'created_by');
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            $user->warrantyClaims()->delete();
            $user->handledWarrantyClaims()->update(['handled_by' => null]);
            $user->replacedWarrantyClaims()->update(['replaced_by' => null]);
            $user->claimHistories()->delete();
            $user->productTraceLogs()->delete();
            $user->createdProducts()->delete();
            $user->createdStandardPackings()->delete();
            $user->createdProjects()->delete();
            $user->projectUsers()->delete();
            $user->distributor()->delete();
        });
    }
}
