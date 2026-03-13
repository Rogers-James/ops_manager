<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = ['company_id', 'employee_id', 'date', 'shift_type_id', 'first_in', 'last_out', 'worked_minutes', 'late_minutes', 'early_leave_minutes', 'overtime_minutes', 'status'];

    protected $table = 'attendance_records';
    protected $casts = ['date' => 'date', 'first_in' => 'datetime', 'last_out' => 'datetime'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function shiftType()
    {
        return $this->belongsTo(ShiftType::class, 'shift_type_id');
    }
}
