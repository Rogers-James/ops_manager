<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PaySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PayScheduleController extends Controller
{
    public function index()
    {
        $paySchedules = PaySchedule::latest()->paginate(15);
        return view('admin.pages.payroll.pay_schedules', compact('paySchedules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'frequency' => 'required|in:monthly,weekly,biweekly,semimonthly',
            'pay_day' => 'nullable|integer|min:1|max:31', // monthly
            'week_day' => 'nullable|integer|min:0|max:6', // weekly
            'is_active' => 'nullable|boolean',
        ]);

        try {
            PaySchedule::create([
                'name' => $data['name'],
                'frequency' => $data['frequency'],
                'pay_day' => $data['pay_day'] ?? null,
                'week_day' => $data['week_day'] ?? null,
                'is_active' => (bool)($data['is_active'] ?? true),
            ]);

            return back()->with('success', 'Pay schedule created.');
        } catch (Throwable $e) {
            Log::error('PaySchedule store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create pay schedule.');
        }
    }

    public function update(Request $request, PaySchedule $paySchedule)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'frequency' => 'required|in:monthly,weekly,biweekly,semimonthly',
            'pay_day' => 'nullable|integer|min:1|max:31',
            'week_day' => 'nullable|integer|min:0|max:6',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $paySchedule->update([
                'name' => $data['name'],
                'frequency' => $data['frequency'],
                'pay_day' => $data['pay_day'] ?? null,
                'week_day' => $data['week_day'] ?? null,
                'is_active' => (bool)($data['is_active'] ?? true),
            ]);

            return back()->with('success', 'Pay schedule updated.');
        } catch (Throwable $e) {
            Log::error('PaySchedule update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update pay schedule.');
        }
    }

    public function destroy(PaySchedule $paySchedule)
    {
        try {
            $paySchedule->delete();
            return back()->with('success', 'Pay schedule deleted.');
        } catch (Throwable $e) {
            Log::error('PaySchedule delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete pay schedule.');
        }
    }
}
