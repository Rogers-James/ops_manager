<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SalaryStructure extends Model
{
    protected $fillable = ['company_id','name','pay_schedule_id'];

    protected $table = 'salary_structures';
    public function company() { return $this->belongsTo(Company::class); }
    public function paySchedule() { return $this->belongsTo(PaySchedule::class); }
    public function items() { return $this->hasMany(SalaryStructureItem::class); }
    public function employeeAssignments() { return $this->hasMany(EmployeeSalaryStructure::class); }

}
