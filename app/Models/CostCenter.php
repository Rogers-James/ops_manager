<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class CostCenter extends Model
{
    protected $fillable = ['company_id','code','name'];

    public function company() { return $this->belongsTo(Company::class); }
    public function employments() { return $this->hasMany(EmployeeEmployment::class); }

}
