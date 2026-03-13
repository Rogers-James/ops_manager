<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['company_id', 'name', 'slug', 'module'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role')->withTimestamps();
    }
}
