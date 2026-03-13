<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CompanyController extends Controller
{
    // single tenant helper
    private function company(): Company
    {
        return Company::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'My Company',
                'timezone' => 'UTC',
                'currency_code' => 'USD',
            ]
        );
    }

    public function edit()
    {
        $company = $this->company();
        return view('admin.pages.setup.index', compact('company'));
    }

    public function update(Request $request)
    {
        $company = $this->company();

        $data = $request->validate([
            'name'            => 'required|string|max:120',
            'legal_name'      => 'nullable|string|max:160',
            'website'         => 'nullable|string|max:200',
            'email'           => 'nullable|email|max:120',
            'phone'           => 'nullable|string|max:40',

            'timezone'        => 'required|string|max:60',
            'currency_code'   => 'required|string|max:10',
            'date_format'     => 'nullable|string|max:30',

            'hq_address'      => 'nullable|string|max:500',
            'city'            => 'nullable|string|max:80',
            'state'           => 'nullable|string|max:80',
            'postal_code'     => 'nullable|string|max:20',
            'country'         => 'nullable|string|max:80',

            'registration_no' => 'nullable|string|max:60',
            'tax_id'          => 'nullable|string|max:60',
        ]);
        // dd($request->all());

        try {
            $company->update($data);
            return back()->with('success', 'Company profile updated successfully!');
        } catch (Throwable $e) {
            Log::error('Company update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong while updating company.');
        }
    }

    public function updateLogo(Request $request)
    {
        $company = $this->company();

        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png|max:800', // 800KB
        ]);

        try {
            $file = $request->file('logo');

            // delete old logo
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }

            $path = $file->storeAs(
                'company/logo',
                'logo_' . time() . '.' . $file->getClientOriginalExtension(),
                'public'
            );

            $company->update(['logo_path' => $path]);

            return back()->with('success', 'Company logo updated!');
        } catch (Throwable $e) {
            Log::error('Company logo update failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while uploading logo.');
        }
    }
}
