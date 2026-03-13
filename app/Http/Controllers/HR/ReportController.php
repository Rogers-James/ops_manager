<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\HrRequest;
use App\Models\LeaveRequest;
use App\Models\PayrollRun;
use App\Models\PayrollRunItem;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function employeeReports(Request $request)
    {
        $query = Employee::with(['employment.department', 'employment.designation', 'employment.location'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->paginate(20)->withQueryString();

        return view('admin.pages.reports.employee_reports', compact('employees'));
    }

    public function attendanceReports(Request $request)
    {
        $query = AttendanceRecord::with(['employee', 'shiftType'])->latest('date');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $records = $query->paginate(20)->withQueryString();
        $employees = Employee::orderBy('first_name')->get(['id', 'employee_code', 'first_name', 'last_name']);

        return view('admin.pages.reports.attendance_reports', compact('records', 'employees'));
    }

    public function leaveReports(Request $request)
    {
        $query = LeaveRequest::with(['employee', 'leaveType'])->latest();

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(20)->withQueryString();
        $employees = Employee::orderBy('first_name')->get(['id', 'employee_code', 'first_name', 'last_name']);

        return view('admin.pages.reports.leave_reports', compact('requests', 'employees'));
    }

    public function payrollReports(Request $request)
    {
        $query = PayrollRunItem::with(['employee', 'payrollRun'])->latest();

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $items = $query->paginate(20)->withQueryString();
        $employees = Employee::orderBy('first_name')->get(['id', 'employee_code', 'first_name', 'last_name']);
        $runs = PayrollRun::latest()->get();

        return view('admin.pages.reports.payroll_reports', compact('items', 'employees', 'runs'));
    }

    public function auditLogs(Request $request)
    {
        // if you have audit_logs table/model use it here
        // for now using HrRequest activity as placeholder
        $logs = HrRequest::with(['employee', 'type'])->latest()->paginate(20);

        return view('admin.pages.reports.audit_logs', compact('logs'));
    }
}
