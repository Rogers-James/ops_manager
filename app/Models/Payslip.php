<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Payslip extends Model
{
    protected $fillable = ['company_id','payroll_run_item_id','employee_id','issue_date'];

    protected $table = 'payslips';
    protected $casts = ['issue_date'=>'date'];
    public function company() { return $this->belongsTo(Company::class); }
    public function runItem() { return $this->belongsTo(PayrollRunItem::class,'payroll_run_item_id'); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function items() { return $this->hasMany(PayslipItem::class); }

}
