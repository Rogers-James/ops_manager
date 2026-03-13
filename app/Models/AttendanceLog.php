<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = ['company_id', 'employee_id', 'attendance_device_id', 'log_time', 'source', 'meta'];

    protected $table = 'attendance_logs';
    protected $casts = ['meta' => 'array', 'log_time' => 'datetime'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function device()
    {
        return $this->belongsTo(AttendanceDevice::class, 'attendance_device_id');
    }
}
