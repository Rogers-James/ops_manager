<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class EmployeeEmployment extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['company_id', 'employee_id', 'location_id', 'department_id', 'designation_id', 'grade_id', 'cost_center_id', 'manager_id', 'employment_type', 'joining_date', 'confirmation_date', 'exit_date'];

    protected $table = 'employee_employment';
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }
}
