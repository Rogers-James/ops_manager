<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class EmployeeRecurringComponent extends Model
{
    protected $fillable = ['company_id','employee_id','salary_component_id','amount','effective_from','effective_to'];

    protected $table = 'employee_recurring_components';
    protected $casts = ['effective_from'=>'date','effective_to'=>'date'];
    public function company() { return $this->belongsTo(Company::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function component() { return $this->belongsTo(SalaryComponent::class,'salary_component_id'); }

}
