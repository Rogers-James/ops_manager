<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['company_id', 'name', 'parent_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }
    public function employments()
    {
        return $this->hasMany(EmployeeEmployment::class);
    }
}
