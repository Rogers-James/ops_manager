<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequest;
use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\PayrollRun;
use App\Models\PayrollRunItem;
use App\Models\Resignation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViewsController extends Controller
{
    public function indexPage()
    {
        $admin = Auth::user(); // or auth('admin')->user();
        $companyId = $admin->company_id;

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Top cards
        $totalEmployees = Employee::where('company_id', $companyId)
            ->where('status', '!=', 'exited')
            ->count();

        $activeEmployees = Employee::where('company_id', $companyId)
            ->where('status', 'active')
            ->count();

        $presentToday = AttendanceRecord::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->where('status', 'present')
            ->count();

        $onLeaveToday = AttendanceRecord::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->where('status', 'leave')
            ->count();

        $lateToday = AttendanceRecord::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->where('late_minutes', '>', 0)
            ->count();

        $pendingApprovals = ApprovalRequest::where('company_id', $companyId)
            ->where('status', 'pending')
            ->count();

        $openExits = Resignation::where('company_id', $companyId)
            ->whereIn('status', ['submitted'])
            ->count();

        // Leave snapshot
        $pendingLeaveRequests = LeaveRequest::where('company_id', $companyId)
            ->where('status', 'submitted')
            ->count();

        $approvedLeavesThisMonth = LeaveRequest::where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereBetween('start_date', [$startOfMonth, $endOfMonth])
            ->count();

        // Payroll snapshot
        $currentPayrollRun = PayrollRun::where('company_id', $companyId)
            ->whereBetween('period_start', [$startOfMonth, $endOfMonth])
            ->latest('id')
            ->first();

        $payrollStatus = $currentPayrollRun?->status ?? 'not_started';

        $thisMonthPayrollNet = 0;
        if ($currentPayrollRun) {
            $thisMonthPayrollNet = (float) PayrollRunItem::where('company_id', $companyId)
                ->where('payroll_run_id', $currentPayrollRun->id)
                ->sum('net');
        }

        // Exceptions / setup gaps
        $employeesWithoutSalary = Employee::where('company_id', $companyId)
            ->where('status', '!=', 'exited')
            ->whereDoesntHave('salaryStructures', function ($q) use ($today) {
                $q->whereDate('effective_from', '<=', $today)
                    ->where(function ($q2) use ($today) {
                        $q2->whereNull('effective_to')
                            ->orWhereDate('effective_to', '>=', $today);
                    });
            })
            ->count();

        $employeesWithoutBank = Employee::where('company_id', $companyId)
            ->where('status', '!=', 'exited')
            ->whereDoesntHave('bankAccounts')
            ->count();

        $employeesWithoutShift = Employee::where('company_id', $companyId)
            ->where('status', '!=', 'exited')
            ->whereDoesntHave('shiftAssignments', function ($q) use ($today) {
                $q->whereDate('effective_from', '<=', $today)
                    ->where(function ($q2) use ($today) {
                        $q2->whereNull('effective_to')
                            ->orWhereDate('effective_to', '>=', $today);
                    });
            })
            ->count();

        $employeesWithoutLeavePolicy = Employee::where('company_id', $companyId)
            ->where('status', '!=', 'exited')
            ->whereDoesntHave('leaveBalances')
            ->count();

        // Recent attendance
        $todayAttendance = AttendanceRecord::with(['employee.employment.department', 'shiftType'])
            ->where('company_id', $companyId)
            ->whereDate('date', $today)
            ->latest('first_in')
            ->take(8)
            ->get();

        // Pending approvals list
        $latestApprovals = ApprovalRequest::with('request')
            ->where('company_id', $companyId)
            ->where('status', 'pending')
            ->latest('id')
            ->take(8)
            ->get();

        // Recent activity
        $recentActivities = AuditLog::with('user')
            ->where('company_id', $companyId)
            ->latest('id')
            ->take(8)
            ->get();

        // Department headcount
        $departmentHeadcount = Employee::query()
            ->join('employee_employment', 'employee_employment.employee_id', '=', 'employees.id')
            ->join('departments', 'departments.id', '=', 'employee_employment.department_id')
            ->where('employees.company_id', $companyId)
            ->where('employees.status', '!=', 'exited')
            ->groupBy('departments.name')
            ->orderByRaw('COUNT(*) DESC')
            ->select('departments.name', DB::raw('COUNT(*) as total'))
            ->take(6)
            ->get();

        // Last 7 days attendance trend
        $attendanceTrend = AttendanceRecord::query()
            ->where('company_id', $companyId)
            ->whereBetween('date', [Carbon::today()->subDays(6), Carbon::today()])
            ->selectRaw('date,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN status = "leave" THEN 1 ELSE 0 END) as leave_count,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.pages.index', compact(
            'admin',
            'today',
            'totalEmployees',
            'activeEmployees',
            'presentToday',
            'onLeaveToday',
            'lateToday',
            'pendingApprovals',
            'openExits',
            'pendingLeaveRequests',
            'approvedLeavesThisMonth',
            'currentPayrollRun',
            'payrollStatus',
            'thisMonthPayrollNet',
            'employeesWithoutSalary',
            'employeesWithoutBank',
            'employeesWithoutShift',
            'employeesWithoutLeavePolicy',
            'todayAttendance',
            'latestApprovals',
            'recentActivities',
            'departmentHeadcount',
            'attendanceTrend'
        ));
    }

    public function tablesPage()
    {
        return view('admin.pages.tables');
    }
}
