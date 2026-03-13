<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class EmployeeSalaryStructure extends Model
{
    protected $fillable = ['company_id','employee_id','salary_structure_id','effective_from','effective_to'];

    protected $table = 'employee_salary_structures';
    protected $casts = ['effective_from'=>'date','effective_to'=>'date'];
    public function company() { return $this->belongsTo(Company::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function structure() { return $this->belongsTo(SalaryStructure::class,'salary_structure_id'); }

}
