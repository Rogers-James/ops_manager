<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class EmployeeKpi extends Model
{
    protected $fillable = ['company_id','employee_id','performance_cycle_id','kpis'];

    protected $table = 'employee_kpis';
    protected $casts = ['kpis'=>'array'];
    public function company() { return $this->belongsTo(Company::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function cycle() { return $this->belongsTo(PerformanceCycle::class,'performance_cycle_id'); }

}
