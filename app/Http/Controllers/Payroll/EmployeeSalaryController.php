<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\SalaryStructure;
use App\Models\EmployeeSalaryStructure; // assignment table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmployeeSalaryController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('first_name')->get(['id','employee_code','first_name','last_name']);
        $structures = SalaryStructure::orderBy('name')->get();

        $assignments = EmployeeSalaryStructure::with(['employee','structure'])
            ->latest()->paginate(15);

        return view('admin.pages.payroll.employee_salary_assignment', compact('employees','structures','assignments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary_structure_id' => 'required|exists:salary_structures,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            EmployeeSalaryStructure::create([
                'employee_id' => $data['employee_id'],
                'salary_structure_id' => $data['salary_structure_id'],
                'effective_from' => $data['effective_from'],
                'effective_to' => $data['effective_to'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            return back()->with('success', 'Salary structure assigned to employee.');
        } catch (Throwable $e) {
            Log::error('EmployeeSalary store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to assign salary structure.');
        }
    }

    public function update(Request $request, EmployeeSalaryStructure $assignment)
    {
        $data = $request->validate([
            'salary_structure_id' => 'required|exists:salary_structures,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $assignment->update([
                'salary_structure_id' => $data['salary_structure_id'],
                'effective_from' => $data['effective_from'],
                'effective_to' => $data['effective_to'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            return back()->with('success', 'Assignment updated.');
        } catch (Throwable $e) {
            Log::error('EmployeeSalary update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update assignment.');
        }
    }

    public function destroy(EmployeeSalaryStructure $assignment)
    {
        try {
            $assignment->delete();
            return back()->with('success', 'Assignment deleted.');
        } catch (Throwable $e) {
            Log::error('EmployeeSalary delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete assignment.');
        }
    }
}
