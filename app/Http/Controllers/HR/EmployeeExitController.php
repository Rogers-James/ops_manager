<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ExitClearance;
use App\Models\ExitClearanceTask;
use App\Models\FinalSettlement;
use App\Models\PayrollRun;
use App\Models\Resignation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmployeeExitController extends Controller
{
    public function index()
    {
        $exits = Resignation::with('employee')->latest()->paginate(15);
        $employees = Employee::orderBy('first_name')->get(['id', 'employee_code', 'first_name', 'last_name']);

        return view('admin.pages.employees.exits', compact('exits', 'employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id'       => 'required|exists:employees,id',
            'resignation_date'  => 'required|date',
            'last_working_day'  => 'required|date|after_or_equal:resignation_date',
            'reason'            => 'nullable|string|max:1000',
        ]);

        try {
            Resignation::create([
                'company_id' => 1,
                'employee_id' => $data['employee_id'],
                'resignation_date' => $data['resignation_date'],
                'last_working_day' => $data['last_working_day'],
                'reason' => $data['reason'] ?? null,
                'status' => 'submitted',
            ]);

            return back()->with('success', 'Exit request created.');
        } catch (Throwable $e) {
            Log::error('Exit store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong while creating exit.');
        }
    }

    public function updateStatus(Request $request, Resignation $resignation)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,rejected,withdrawn',
        ]);

        try {
            DB::beginTransaction();

            $resignation->update(['status' => $data['status']]);

            // When approved => mark exit in employment + optionally deactivate employee
            if ($data['status'] === 'approved') {
                $employee = $resignation->employee;

                if ($employee->employment) {
                    $employee->employment->update([
                        'exit_date' => $resignation->last_working_day,
                    ]);
                }

                // Most HRMs deactivate employee when exit approved (you can also do it after last_working_day via cron later)
                $employee->update(['status' => 'inactive']);
            }

            DB::commit();
            return back()->with('success', 'Exit status updated.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Exit status update failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while updating exit.');
        }
    }

    public function showExitClearance(Resignation $resignation)
    {
        try {
            // single-tenant (adjust later for SaaS)
            // $companyId = 1;

            $resignation->load('employee');

            // create clearance if not exists
            $clearance = ExitClearance::firstOrCreate(
                [
                    // 'company_id' => $companyId,
                    'resignation_id' => $resignation->id,
                ],
                [
                    'initiated_on' => now()->toDateString(),
                    'status' => 'open',
                ]
            );

            $tasks = ExitClearanceTask::where('exit_clearance_id', $clearance->id)
                ->latest()
                ->get();

            $finalSettlement = FinalSettlement::where('resignation_id', $resignation->id)
                ->first();

            // Optional: if you have payroll module already
            $payrollRuns = class_exists(PayrollRun::class)
                ? PayrollRun::latest()->limit(50)->get()
                : null;

            return view('admin.pages.employees.exit_clearance', compact(
                'resignation',
                'clearance',
                'tasks',
                'finalSettlement',
                'payrollRuns'
            ));
        } catch (Throwable $e) {
            Log::error('showExitClearance error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to open exit clearance page.');
        }
    }

    public function saveFinalSettlement(Request $request, Resignation $resignation)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|max:30',
            'payroll_run_id' => 'nullable|integer',
        ]);

        try {
            $companyId = 1;

            FinalSettlement::updateOrCreate(
                [
                    'company_id' => $companyId,
                    'resignation_id' => $resignation->id,
                ],
                [
                    'employee_id' => $resignation->employee_id,
                    'payroll_run_id' => $data['payroll_run_id'] ?? null,
                    'amount' => $data['amount'],
                    'status' => $data['status'],
                ]
            );

            // also update clearance status to in_progress if it was open
            ExitClearance::where('company_id', $companyId)
                ->where('resignation_id', $resignation->id)
                ->where('status', 'open')
                ->update(['status' => 'in_progress']);

            return back()->with('success', 'Final settlement saved successfully!');
        } catch (Throwable $e) {
            Log::error('saveFinalSettlement error', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong while saving final settlement.');
        }
    }

    public function addTask(Request $request)
    {
        $data = $request->validate([
            'exit_clearance_id' => 'required|integer|exists:exit_clearances,id',
            'module' => 'required|in:hr,it,finance,admin',
            'title'  => 'required|string|max:120',
            'notes'  => 'nullable|string|max:500',
        ]);

        try {
            $companyId = 1;

            // security: ensure this clearance belongs to this company
            $clearance = ExitClearance::where('company_id', $companyId)
                ->where('id', $data['exit_clearance_id'])
                ->first();

            if (!$clearance) {
                return back()->with('error', 'Invalid clearance record.');
            }

            ExitClearanceTask::create([
                'company_id' => $companyId,
                'exit_clearance_id' => $clearance->id,
                'module' => $data['module'],
                'title' => $data['title'],
                'notes' => $data['notes'] ?? null,
                'status' => 'pending',
                'action_by' => null,
                'action_at' => null,
            ]);

            // mark clearance started
            if ($clearance->status === 'open') {
                $clearance->update(['status' => 'in_progress']);
            }

            return back()->with('success', 'Clearance task added.');
        } catch (Throwable $e) {
            Log::error('addTask error', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong while adding task.');
        }
    }

    public function updateTaskStatus(Request $request, ExitClearanceTask $task)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        try {
            $companyId = 1;

            // security: company match
            if ((int)$task->company_id !== (int)$companyId) {
                abort(403);
            }

            $task->update([
                'status' => $data['status'],
                'action_by' => Auth::id(),
                'action_at' => now(),
            ]);

            return back()->with('success', 'Task status updated.');
        } catch (Throwable $e) {
            Log::error('updateTaskStatus error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while updating task.');
        }
    }

    public function updateClearanceStatus(Request $request, Resignation $resignation)
    {
        $data = $request->validate([
            'status' => 'required|in:open,in_progress,cleared,hold',
        ]);

        try {
            $companyId = 1;

            $clearance = ExitClearance::where('company_id', $companyId)
                ->where('resignation_id', $resignation->id)
                ->first();

            if (!$clearance) {
                return back()->with('error', 'Clearance record not found.');
            }

            // If trying to clear, ensure all tasks are approved
            if ($data['status'] === 'cleared') {
                $notApproved = ExitClearanceTask::where('company_id', $companyId)
                    ->where('exit_clearance_id', $clearance->id)
                    ->where('status', '!=', 'approved')
                    ->count();

                if ($notApproved > 0) {
                    return back()->with('error', 'Approve all tasks before marking clearance as cleared.');
                }
            }

            $clearance->update(['status' => $data['status']]);

            return back()->with('success', 'Clearance status updated.');
        } catch (Throwable $e) {
            Log::error('updateClearanceStatus error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while updating clearance status.');
        }
    }
}
