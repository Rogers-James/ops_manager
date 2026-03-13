<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollRun;
use App\Models\Employee;
use App\Models\PayrollRunItem;

class PayslipController extends Controller
{
    public function index()
    {
        $runs = PayrollRun::latest()->paginate(15);
        return view('admin.pages.payroll.payslips', compact('runs'));
    }

    public function show(PayrollRun $payrollRun, Employee $employee)
    {
        $item = PayrollRunItem::with(['employee','payrollRun.paySchedule'])
            ->where('payroll_run_id', $payrollRun->id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        return view('admin.pages.payroll.payslip_show', compact('item'));
    }
}
