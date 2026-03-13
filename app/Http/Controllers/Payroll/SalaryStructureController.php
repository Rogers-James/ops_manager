<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\SalaryStructure;
use App\Models\SalaryComponent;
use App\Models\SalaryStructureItem; // pivot/line table
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SalaryStructureController extends Controller
{
    public function index()
    {
        $components = SalaryComponent::orderBy('name')->get();
        $structures = SalaryStructure::with(['items.component'])->latest()->paginate(10);

        return view('admin.pages.payroll.salary_structures', compact('structures','components'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'currency_code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            SalaryStructure::create([
                'name' => $data['name'],
                'currency_code' => $data['currency_code'] ?? null,
                'is_active' => (bool)($data['is_active'] ?? true),
            ]);

            return back()->with('success', 'Salary structure created.');
        } catch (Throwable $e) {
            Log::error('SalaryStructure store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create structure.');
        }
    }

    public function update(Request $request, SalaryStructure $salaryStructure)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'currency_code' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $salaryStructure->update([
                'name' => $data['name'],
                'currency_code' => $data['currency_code'] ?? null,
                'is_active' => (bool)($data['is_active'] ?? true),
            ]);

            return back()->with('success', 'Structure updated.');
        } catch (Throwable $e) {
            Log::error('SalaryStructure update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update structure.');
        }
    }

    public function destroy(SalaryStructure $salaryStructure)
    {
        try {
            $salaryStructure->delete();
            return back()->with('success', 'Structure deleted.');
        } catch (Throwable $e) {
            Log::error('SalaryStructure delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete structure.');
        }
    }

    public function addItem(Request $request, SalaryStructure $salaryStructure)
    {
        $data = $request->validate([
            'salary_component_id' => 'required|exists:salary_components,id',
            'amount' => 'nullable|numeric|min:0',
            'percent' => 'nullable|numeric|min:0|max:100',
            'formula' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0|max:999',
        ]);

        try {
            SalaryStructureItem::create([
                'salary_structure_id' => $salaryStructure->id,
                'salary_component_id' => $data['salary_component_id'],
                'amount' => $data['amount'] ?? null,
                'percent' => $data['percent'] ?? null,
                'formula' => $data['formula'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            return back()->with('success', 'Component added to structure.');
        } catch (Throwable $e) {
            Log::error('Structure addItem failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to add component.');
        }
    }

    public function removeItem(SalaryStructureItem $item)
    {
        try {
            $item->delete();
            return back()->with('success', 'Structure item removed.');
        } catch (Throwable $e) {
            Log::error('Structure removeItem failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to remove item.');
        }
    }
}
