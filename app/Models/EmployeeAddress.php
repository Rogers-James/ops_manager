<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class EmployeeAddress extends Model
{
    protected $fillable = ['company_id','employee_id','type','line1','line2','city','state','country','postal_code'];

    public function company() { return $this->belongsTo(Company::class); }
    public function employee() { return $this->belongsTo(Employee::class); }

}
