<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class LoanRepayment extends Model
{
    protected $fillable = ['company_id','employee_loan_id','payroll_run_id','amount','paid_on'];

    protected $table = 'loan_repayments';
    protected $casts = ['amount'=>'decimal:2','paid_on'=>'date'];
    public function company() { return $this->belongsTo(Company::class); }
    public function loan() { return $this->belongsTo(EmployeeLoan::class,'employee_loan_id'); }
    public function payrollRun() { return $this->belongsTo(PayrollRun::class); }

}
