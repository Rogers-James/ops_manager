<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PaySchedule;
use App\Models\PayrollRun;
use App\Models\PayrollRunItem;
use App\Models\EmployeeSalaryStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PayrollProcessingController extends Controller
{
    public function create()
    {
        $schedules = PaySchedule::orderBy('name')->get();
        return view('admin.pages.payroll.run_payroll', compact('schedules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pay_schedule_id' => 'required|exists:pay_schedules,id',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
            'notes'        => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Prevent duplicate run for same schedule+period
            $exists = PayrollRun::where('pay_schedule_id', $data['pay_schedule_id'])
                ->whereDate('period_start', $data['period_start'])
                ->whereDate('period_end', $data['period_end'])
                ->exists();

            if ($exists) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Payroll run already exists for this period.');
            }

            $run = PayrollRun::create([
                'pay_schedule_id' => $data['pay_schedule_id'],
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
                'status' => 'draft', // draft -> processed -> approved -> paid
                'notes' => $data['notes'] ?? null,
            ]);

            /**
             * GENERATE ITEMS:
             * - Find active employees
             * - Find their current salary assignment (EmployeeSalaryStructure)
             * - Compute gross/deductions/net
             *
             * For now we keep it simple:
             * - gross = sum of “earning” items in structure
             * - deductions = sum of “deduction” items in structure
             * - net = gross - deductions
             */
            $employees = Employee::where('status', 'active')->get();

            foreach ($employees as $emp) {
                $assignment = EmployeeSalaryStructure::with('structure.items.component')
                    ->where('employee_id', $emp->id)
                    ->whereDate('effective_from', '<=', $data['period_end'])
                    ->where(function($q) use ($data){
                        $q->whereNull('effective_to')
                          ->orWhereDate('effective_to', '>=', $data['period_start']);
                    })
                    ->latest('effective_from')
                    ->first();

                // if no salary assignment, skip employee
                if (!$assignment || !$assignment->structure) continue;

                $gross = 0;
                $deductions = 0;

                foreach ($assignment->structure->items as $item) {
                    $component = $item->component;
                    if (!$component) continue;

                    // compute value by calc_type
                    $value = 0;

                    if ($component->calc_type === 'fixed') {
                        $value = (float)($item->amount ?? 0);
                    } elseif ($component->calc_type === 'percent') {
                        // percent of current gross (simple approach)
                        $value = $gross * ((float)($item->percent ?? 0) / 100);
                    } elseif ($component->calc_type === 'formula') {
                        // TODO: later implement formula parser
                        $value = (float)($item->amount ?? 0);
                    }

                    if ($component->type === 'earning') $gross += $value;
                    if ($component->type === 'deduction') $deductions += $value;
                }

                $net = max(0, $gross - $deductions);

                PayrollRunItem::create([
                    'payroll_run_id' => $run->id,
                    'employee_id' => $emp->id,
                    'gross_amount' => $gross,
                    'deduction_amount' => $deductions,
                    'net_amount' => $net,
                    'status' => 'draft',
                    'meta' => [
                        'salary_structure_id' => $assignment->salary_structure_id,
                    ],
                ]);
            }

            DB::commit();
            return redirect()->route('admin.payroll_runs.show', $run)->with('success', 'Payroll run generated!');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Run payroll failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to run payroll. Check logs.');
        }
    }
}
