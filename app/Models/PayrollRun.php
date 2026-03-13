<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRun extends Model
{
    protected $fillable = ['company_id', 'pay_schedule_id', 'period_start', 'period_end', 'status', 'workflow_id'];

    protected $table = 'payroll_runs';
    protected $casts = ['period_start' => 'date', 'period_end' => 'date'];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function paySchedule()
    {
        return $this->belongsTo(PaySchedule::class);
    }
    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
    public function items()
    {
        return $this->hasMany(PayrollRunItem::class);
    }
    public function finalSettlements()
    {
        return $this->hasMany(FinalSettlement::class);
    }
}
