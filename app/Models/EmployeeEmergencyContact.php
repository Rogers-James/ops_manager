<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class EmployeeEmergencyContact extends Model
{
    protected $fillable = ['company_id','employee_id','name','relation','phone'];

    public function company() { return $this->belongsTo(Company::class); }
    public function employee() { return $this->belongsTo(Employee::class); }

}
