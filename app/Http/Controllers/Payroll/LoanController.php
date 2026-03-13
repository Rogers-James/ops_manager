<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeLoan; // your zip already had EmployeeLoan model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class LoanController extends Controller
{
    public function index()
    {
        $employees = Employee::orderBy('first_name')->get(['id','employee_code','first_name','last_name']);
        $loans = EmployeeLoan::with('employee')->latest()->paginate(15);

        return view('admin.pages.payroll.loans', compact('employees','loans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:loan,advance',
            'amount' => 'required|numeric|min:0.01',
            'installment_amount' => 'nullable|numeric|min:0.01',
            'start_month' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            EmployeeLoan::create([
                'employee_id' => $data['employee_id'],
                'type' => $data['type'],
                'amount' => $data['amount'],
                'installment_amount' => $data['installment_amount'] ?? null,
                'start_month' => $data['start_month'] ?? null,
                'status' => 'active',
                'notes' => $data['notes'] ?? null,
            ]);

            return back()->with('success', 'Loan/Advance added.');
        } catch (Throwable $e) {
            Log::error('Loan store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to add loan/advance.');
        }
    }

    public function update(Request $request, EmployeeLoan $loan)
    {
        $data = $request->validate([
            'status' => 'required|in:active,closed,cancelled',
            'installment_amount' => 'nullable|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $loan->update([
                'status' => $data['status'],
                'installment_amount' => $data['installment_amount'] ?? $loan->installment_amount,
                'notes' => $data['notes'] ?? $loan->notes,
            ]);

            return back()->with('success', 'Loan updated.');
        } catch (Throwable $e) {
            Log::error('Loan update failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to update loan.');
        }
    }

    public function destroy(EmployeeLoan $loan)
    {
        try {
            $loan->delete();
            return back()->with('success', 'Loan deleted.');
        } catch (Throwable $e) {
            Log::error('Loan delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete loan.');
        }
    }
}
