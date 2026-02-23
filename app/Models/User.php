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

    public function sales()
    {
        return $this->hasMany(Sale::class, 'buyer_user_id');
    }

    public function warrantyClaims()
    {
        return $this->hasMany(WarrantyClaim::class, 'claimed_by_user_id');
    }
}
