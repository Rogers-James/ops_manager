<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\AttendanceRecord;
use App\Models\AttendanceRequest;
use App\Models\AttendanceDevice;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class AttendanceController extends Controller
{
    // ---------------------------
    // 1) DAILY ATTENDANCE (Records)
    // ---------------------------
    public function daily(Request $request)
    {
        $companyId = 1;

        $date = $request->get('date', now()->toDateString());

        // List all employees (best for manual admin marking)
        $employees = Employee::orderBy('first_name')->get(['id', 'employee_code', 'first_name', 'last_name']);

        // records for selected date
        $records = AttendanceRecord::with(['employee', 'shiftType'])
            ->where('company_id', $companyId)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('employee_id');

        return view('admin.pages.attendance.daily', compact('employees', 'records', 'date'));
    }

    public function markDaily(Request $request)
    {
        $companyId = 1;

        $data = $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'date'          => 'required|date',
            'status'        => 'required|string|max:30',
            'shift_type_id' => 'nullable|integer',
            'first_in'      => 'nullable|date',
            'last_out'      => 'nullable|date|after_or_equal:first_in',
        ]);

        try {
            DB::beginTransaction();

            $worked = 0;
            if (!empty($data['first_in']) && !empty($data['last_out'])) {
                $worked = now()->parse($data['first_in'])->diffInMinutes(now()->parse($data['last_out']));
            }

            AttendanceRecord::updateOrCreate(
                [
                    'company_id' => $companyId,
                    'employee_id' => $data['employee_id'],
                    'date' => $data['date'],
                ],
                [
                    'shift_type_id' => $data['shift_type_id'] ?? null,
                    'first_in' => $data['first_in'] ?? null,
                    'last_out' => $data['last_out'] ?? null,
                    'worked_minutes' => $worked,
                    'late_minutes' => 0,
                    'early_leave_minutes' => 0,
                    'overtime_minutes' => 0,
                    'status' => $data['status'],
                ]
            );

            // OPTIONAL BUT RECOMMENDED: create logs for manual entry (keeps global consistency)
            if (!empty($data['first_in'])) {
                AttendanceLog::create([
                    'company_id' => $companyId,
                    'employee_id' => $data['employee_id'],
                    'attendance_device_id' => null,
                    'log_time' => $data['first_in'],
                    'source' => 'manual',
                    'meta' => ['type' => 'IN'],
                ]);
            }
            if (!empty($data['last_out'])) {
                AttendanceLog::create([
                    'company_id' => $companyId,
                    'employee_id' => $data['employee_id'],
                    'attendance_device_id' => null,
                    'log_time' => $data['last_out'],
                    'source' => 'manual',
                    'meta' => ['type' => 'OUT'],
                ]);
            }

            DB::commit();
            return back()->with('success', 'Attendance saved.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('markDaily failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Database error while saving attendance.');
        }
    }

    public function updateDaily(Request $request, AttendanceRecord $record)
    {
        $data = $request->validate([
            'status'        => 'required|string|max:30',
            'shift_type_id' => 'nullable|integer',
            'first_in'      => 'nullable|date',
            'last_out'      => 'nullable|date|after_or_equal:first_in',
        ]);

        try {
            $worked = 0;
            if (!empty($data['first_in']) && !empty($data['last_out'])) {
                $worked = now()->parse($data['first_in'])->diffInMinutes(now()->parse($data['last_out']));
            }

            $record->update([
                'shift_type_id' => $data['shift_type_id'] ?? null,
                'first_in' => $data['first_in'] ?? null,
                'last_out' => $data['last_out'] ?? null,
                'worked_minutes' => $worked,
                'status' => $data['status'],
            ]);

            return back()->with('success', 'Attendance updated.');
        } catch (Throwable $e) {
            Log::error('updateDaily failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update attendance.');
        }
    }

    // Process logs -> records for a date (used later for device sync / auto attendance)
    public function processDate(Request $request)
    {
        $companyId = 1;
        $data = $request->validate([
            'date' => 'required|date',
        ]);

        try {
            $date = $data['date'];

            $logs = AttendanceLog::where('company_id', $companyId)
                ->whereDate('log_time', $date)
                ->orderBy('log_time')
                ->get()
                ->groupBy('employee_id');

            foreach ($logs as $employeeId => $empLogs) {
                $firstIn = $empLogs->min('log_time');
                $lastOut = $empLogs->max('log_time');

                $worked = 0;
                if ($firstIn && $lastOut) {
                    $worked = now()->parse($firstIn)->diffInMinutes(now()->parse($lastOut));
                }

                AttendanceRecord::updateOrCreate(
                    ['company_id' => $companyId, 'employee_id' => $employeeId, 'date' => $date],
                    [
                        'first_in' => $firstIn,
                        'last_out' => $lastOut,
                        'worked_minutes' => $worked,
                        'status' => $firstIn ? 'present' : 'absent',
                    ]
                );
            }

            return back()->with('success', 'Logs processed into daily records.');
        } catch (Throwable $e) {
            Log::error('processDate failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to process logs.');
        }
    }

    // ---------------------------
    // 2) LOGS
    // ---------------------------
    public function logs(Request $request)
    {
        $companyId = 1;

        $date = $request->get('date', now()->toDateString());
        $employeeId = $request->get('employee_id');

        $employees = Employee::orderBy('first_name')->get(['id', 'employee_code', 'first_name', 'last_name']);
        $devices = AttendanceDevice::where('company_id', $companyId)->orderBy('name')->get();

        $query = AttendanceLog::with(['employee', 'device'])
            ->where('company_id', $companyId)
            ->whereDate('log_time', $date)
            ->latest();

        if ($employeeId) $query->where('employee_id', $employeeId);

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.pages.attendance.logs', compact('logs', 'employees', 'devices', 'date', 'employeeId'));
    }

    public function storeLog(Request $request)
    {
        $companyId = 1;

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'log_time' => 'required|date',
            'attendance_device_id' => 'nullable|integer|exists:attendance_devices,id',
            'source' => 'required|in:manual,device,employee,import',
            'meta' => 'nullable|array',
        ]);

        try {
            AttendanceLog::create([
                'company_id' => $companyId,
                'employee_id' => $data['employee_id'],
                'attendance_device_id' => $data['attendance_device_id'] ?? null,
                'log_time' => $data['log_time'],
                'source' => $data['source'],
                'meta' => $data['meta'] ?? null,
            ]);

            return back()->with('success', 'Log added.');
        } catch (Throwable $e) {
            Log::error('storeLog failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to add log.');
        }
    }

    public function deleteLog(AttendanceLog $log)
    {
        try {
            $log->delete();
            return back()->with('success', 'Log deleted.');
        } catch (Throwable $e) {
            Log::error('deleteLog failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete log.');
        }
    }

    // ---------------------------
    // 3) CORRECTIONS / REGULARIZATION
    // ---------------------------
    public function corrections(Request $request)
    {
        $companyId = 1;

        $status = $request->get('status');
        $query = AttendanceRequest::with('employee')
            ->where('company_id', $companyId)
            ->latest();

        if ($status) $query->where('status', $status);

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.pages.attendance.corrections', compact('requests', 'status'));
    }

    public function updateCorrectionStatus(Request $request, AttendanceRequest $requestModel)
    {
        $companyId = 1;

        $data = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        try {
            DB::beginTransaction();

            if ((int)$requestModel->company_id !== (int)$companyId) abort(403);

            $requestModel->update(['status' => $data['status']]);

            if ($data['status'] === 'approved') {
                // Apply correction to attendance record
                $worked = 0;
                if ($requestModel->requested_first_in && $requestModel->requested_last_out) {
                    $worked = now()->parse($requestModel->requested_first_in)
                        ->diffInMinutes(now()->parse($requestModel->requested_last_out));
                }

                AttendanceRecord::updateOrCreate(
                    [
                        'company_id' => $companyId,
                        'employee_id' => $requestModel->employee_id,
                        'date' => $requestModel->date,
                    ],
                    [
                        'first_in' => $requestModel->requested_first_in,
                        'last_out' => $requestModel->requested_last_out,
                        'worked_minutes' => $worked,
                        'status' => 'present',
                    ]
                );

                // Optional: Insert logs with source=request
                if ($requestModel->requested_first_in) {
                    AttendanceLog::create([
                        'company_id' => $companyId,
                        'employee_id' => $requestModel->employee_id,
                        'attendance_device_id' => null,
                        'log_time' => $requestModel->requested_first_in,
                        'source' => 'request',
                        'meta' => ['type' => 'IN', 'request_id' => $requestModel->id],
                    ]);
                }
                if ($requestModel->requested_last_out) {
                    AttendanceLog::create([
                        'company_id' => $companyId,
                        'employee_id' => $requestModel->employee_id,
                        'attendance_device_id' => null,
                        'log_time' => $requestModel->requested_last_out,
                        'source' => 'request',
                        'meta' => ['type' => 'OUT', 'request_id' => $requestModel->id],
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Correction status updated.');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('updateCorrectionStatus failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to update correction.');
        }
    }

    // ---------------------------
    // 4) OVERTIME
    // ---------------------------
    public function overtime(Request $request)
    {
        $companyId = 1;

        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to', now()->toDateString());

        $records = AttendanceRecord::with('employee')
            ->where('company_id', $companyId)
            ->whereBetween('date', [$from, $to])
            ->where('overtime_minutes', '>', 0)
            ->orderBy('date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.pages.attendance.overtime', compact('records', 'from', 'to'));
    }
}
