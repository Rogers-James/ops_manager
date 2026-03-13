<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PaySchedule extends Model
{
    protected $fillable = ['company_id','name','frequency','pay_day'];

    protected $table = 'pay_schedules';
    public function company() { return $this->belongsTo(Company::class); }
    public function salaryStructures() { return $this->hasMany(SalaryStructure::class); }
    public function payrollRuns() { return $this->hasMany(PayrollRun::class); }

}
