<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollRun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PayrollRunController extends Controller
{
    public function index()
    {
        $runs = PayrollRun::with('paySchedule')->latest()->paginate(15);
        return view('admin.pages.payroll.payroll_runs', compact('runs'));
    }

    public function show(PayrollRun $payrollRun)
    {
        $payrollRun->load(['paySchedule', 'items.employee']);
        return view('admin.pages.payroll.payroll_run_show', compact('payrollRun'));
    }

    public function updateStatus(Request $request, PayrollRun $payrollRun)
    {
        $data = $request->validate([
            'status' => 'required|in:draft,processed,approved,paid,cancelled',
        ]);

        try {
            $payrollRun->update(['status' => $data['status']]);
            return back()->with('success', 'Run status updated.');
        } catch (Throwable $e) {
            Log::error('PayrollRun status update failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to update status.');
        }
    }
}
