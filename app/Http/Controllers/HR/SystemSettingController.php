<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CustomField;
use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\PayrollRunItem;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    public function general()
    {
        $company = Company::first();
        return view('admin.pages.settings.general', compact('company'));
    }

    public function saveGeneral(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'timezone' => 'nullable|string|max:100',
            'currency_code' => 'nullable|string|max:10',
            'date_format' => 'nullable|string|max:30',
            'logo_path' => 'nullable|string|max:255',
        ]);

        $company = Company::first();
        if ($company) {
            $company->update($data);
        } else {
            Company::create($data);
        }

        return back()->with('success', 'General settings updated successfully.');
    }

    public function notifications()
    {
        return view('admin.pages.settings.notifications');
    }

    public function saveNotifications(Request $request)
    {
        // save in settings table if you have one
        return back()->with('success', 'Notification settings updated successfully.');
    }

    public function customFields()
    {
        $fields = CustomField::latest()->paginate(20);
        return view('admin.pages.settings.custom_fields', compact('fields'));
    }

    public function storeCustomField(Request $request)
    {
        $data = $request->validate([
            'module' => 'required|string|max:100',
            'label' => 'required|string|max:150',
            'field_type' => 'required|string|max:50',
            'is_required' => 'nullable|boolean',
        ]);

        CustomField::create([
            'module' => $data['module'],
            'label' => $data['label'],
            'field_type' => $data['field_type'],
            'is_required' => (bool)($data['is_required'] ?? false),
        ]);

        return back()->with('success', 'Custom field created successfully.');
    }

    public function destroyCustomField(CustomField $customField)
    {
        $customField->delete();
        return back()->with('success', 'Custom field deleted successfully.');
    }

    public function backups()
    {
        return view('admin.pages.settings.backups');
    }

    public function exportEmployees()
    {
        $employees = Employee::all();
        return response()->json($employees);
    }

    public function exportAttendance()
    {
        $attendance = AttendanceRecord::all();
        return response()->json($attendance);
    }

    public function exportPayroll()
    {
        $payroll = PayrollRunItem::all();
        return response()->json($payroll);
    }
}
