<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = ['company_id', 'name', 'rank'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function employments()
    {
        return $this->hasMany(EmployeeEmployment::class);
    }
}
