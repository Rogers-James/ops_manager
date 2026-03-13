<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\WorkWeekProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class WorkWeekProfileController extends Controller
{
    public function index()
    {
        $companyId = 1;

        $profiles = WorkWeekProfile::latest()->paginate(15);
        return view('admin.pages.shifts.work_week_profiles', compact('profiles'));
    }

    public function store(Request $request)
    {
        $companyId = 1;

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'mon' => 'nullable|boolean',
            'tue' => 'nullable|boolean',
            'wed' => 'nullable|boolean',
            'thu' => 'nullable|boolean',
            'fri' => 'nullable|boolean',
            'sat' => 'nullable|boolean',
            'sun' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        try {
            if (!empty($data['is_default'])) {
                WorkWeekProfile::update(['is_default' => false]);
            }

            WorkWeekProfile::create([
                // 'company_id' => $companyId,
                'name' => $data['name'],
                'mon' => (bool)($data['mon'] ?? true),
                'tue' => (bool)($data['tue'] ?? true),
                'wed' => (bool)($data['wed'] ?? true),
                'thu' => (bool)($data['thu'] ?? true),
                'fri' => (bool)($data['fri'] ?? true),
                'sat' => (bool)($data['sat'] ?? false),
                'sun' => (bool)($data['sun'] ?? false),
                'is_default' => (bool)($data['is_default'] ?? false),
            ]);

            return back()->with('success', 'Work week profile created.');
        } catch (Throwable $e) {
            Log::error('WorkWeekProfile store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create profile.');
        }
    }

    public function update(Request $request, WorkWeekProfile $profile)
    {
        $companyId = 1;

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'mon' => 'nullable|boolean',
            'tue' => 'nullable|boolean',
            'wed' => 'nullable|boolean',
            'thu' => 'nullable|boolean',
            'fri' => 'nullable|boolean',
            'sat' => 'nullable|boolean',
            'sun' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        try {
            if (!empty($data['is_default'])) {
                WorkWeekProfile::where('id', '!=', $profile->id)
                    ->update(['is_default' => false]);
            }

            $profile->update([
                'name' => $data['name'],
                'mon' => (bool)($data['mon'] ?? false),
                'tue' => (bool)($data['tue'] ?? false),
                'wed' => (bool)($data['wed'] ?? false),
                'thu' => (bool)($data['thu'] ?? false),
                'fri' => (bool)($data['fri'] ?? false),
                'sat' => (bool)($data['sat'] ?? false),
                'sun' => (bool)($data['sun'] ?? false),
                'is_default' => (bool)($data['is_default'] ?? false),
            ]);

            return back()->with('success', 'Profile updated.');
        } catch (Throwable $e) {
            Log::error('WorkWeekProfile update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update profile.');
        }
    }

    public function destroy(WorkWeekProfile $profile)
    {
        try {
            $profile->delete();
            return back()->with('success', 'Profile deleted.');
        } catch (Throwable $e) {
            Log::error('WorkWeekProfile delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete profile.');
        }
    }
}
