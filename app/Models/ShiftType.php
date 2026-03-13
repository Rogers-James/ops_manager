<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ShiftType extends Model
{
    protected $fillable = ['company_id','name','start_time','end_time','break_minutes','grace_minutes','is_night_shift'];

    protected $table = 'shift_types';
    public function company() { return $this->belongsTo(Company::class); }
    public function attendanceRecords() { return $this->hasMany(AttendanceRecord::class,'shift_type_id'); }

}
