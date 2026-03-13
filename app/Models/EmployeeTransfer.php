<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTransfer extends Model
{
    protected $fillable = [
        'company_id',
        'employee_id',
        'from_location_id',
        'from_department_id',
        'from_designation_id',
        'from_grade_id',
        'from_cost_center_id',
        'from_manager_id',
        'to_location_id',
        'to_department_id',
        'to_designation_id',
        'to_grade_id',
        'to_cost_center_id',
        'to_manager_id',
        'effective_date',
        'reason',
        'status'
    ];

    protected $casts = ['effective_date' => 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }
    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function fromDesignation()
    {
        return $this->belongsTo(Designation::class, 'from_designation_id');
    }
    public function toDesignation()
    {
        return $this->belongsTo(Designation::class, 'to_designation_id');
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }
    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }
}
