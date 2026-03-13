<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PerformanceCycle extends Model
{
    protected $fillable = ['company_id','name','start_date','end_date','status'];

    protected $table = 'performance_cycles';
    protected $casts = ['start_date'=>'date','end_date'=>'date'];
    public function company() { return $this->belongsTo(Company::class); }
    public function employeeKpis() { return $this->hasMany(EmployeeKpi::class); }
    public function reviews() { return $this->hasMany(PerformanceReview::class); }

}
