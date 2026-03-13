<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceDevice extends Model
{
    protected $fillable = ['company_id', 'name', 'type', 'ip_address', 'meta'];

    protected $table = 'attendance_devices';
    protected $casts = ['meta' => 'array'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function logs()
    {
        return $this->hasMany(AttendanceLog::class, 'attendance_device_id');
    }
}
