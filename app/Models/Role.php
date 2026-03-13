<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['company_id', 'name', 'slug'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user')->withTimestamps();
    }
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role')->withTimestamps();
    }
}
