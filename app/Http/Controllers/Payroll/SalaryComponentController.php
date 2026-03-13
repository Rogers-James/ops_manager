<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SalaryComponentController extends Controller
{
    public function index()
    {
        $components = SalaryComponent::latest()->paginate(15);
        return view('admin.pages.payroll.salary_components', compact('components'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'code' => 'nullable|string|max:30|unique:salary_components,code',
            'type' => 'required|in:earning,deduction',
            'calc_type' => 'required|in:fixed,percent,formula',
            'is_taxable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            SalaryComponent::create([
                'name' => $data['name'],
                'code' => $data['code'] ?? null,
                'type' => $data['type'],
                'calc_type' => $data['calc_type'],
                'is_taxable' => (bool)($data['is_taxable'] ?? false),
                'is_active' => (bool)($data['is_active'] ?? true),
            ]);

            return back()->with('success', 'Salary component created.');
        } catch (Throwable $e) {
            Log::error('SalaryComponent store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create component.');
        }
    }

    public function update(Request $request, SalaryComponent $salaryComponent)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'code' => 'nullable|string|max:30|unique:salary_components,code,' . $salaryComponent->id,
            'type' => 'required|in:earning,deduction',
            'calc_type' => 'required|in:fixed,percent,formula',
            'is_taxable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $salaryComponent->update([
                'name' => $data['name'],
                'code' => $data['code'] ?? null,
                'type' => $data['type'],
                'calc_type' => $data['calc_type'],
                'is_taxable' => (bool)($data['is_taxable'] ?? false),
                'is_active' => (bool)($data['is_active'] ?? true),
            ]);

            return back()->with('success', 'Salary component updated.');
        } catch (Throwable $e) {
            Log::error('SalaryComponent update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update component.');
        }
    }

    public function destroy(SalaryComponent $salaryComponent)
    {
        try {
            $salaryComponent->delete();
            return back()->with('success', 'Component deleted.');
        } catch (Throwable $e) {
            Log::error('SalaryComponent delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete component.');
        }
    }
}
