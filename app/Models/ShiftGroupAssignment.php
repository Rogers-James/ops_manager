<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftGroupAssignment extends Model
{
    protected $fillable = ['company_id', 'shift_group_id', 'employee_id', 'effective_from', 'effective_to'];

    protected $table = 'shift_group_assignments';
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function group()
    {
        return $this->belongsTo(ShiftGroup::class, 'shift_group_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
