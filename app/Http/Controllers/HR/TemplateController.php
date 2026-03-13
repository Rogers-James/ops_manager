<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::latest()->paginate(15);
        $pageTitle = 'Template Library';
        $filterType = null;

        return view('admin.pages.templatess.index', compact('templates', 'pageTitle', 'filterType'));
    }

    public function offerLetters()
    {
        $filterType = 'offer_letter';
        $pageTitle = 'Offer Letter Templates';

        $templates = Template::where('type', $filterType)
            ->latest()
            ->paginate(15);

        return view('admin.pages.templatess.index', compact('templates', 'pageTitle', 'filterType'));
    }

    public function experienceLetters()
    {
        $filterType = 'experience_letter';
        $pageTitle = 'Experience Letter Templates';

        $templates = Template::where('type', $filterType)
            ->latest()
            ->paginate(15);

        return view('admin.pages.templatess.index', compact('templates', 'pageTitle', 'filterType'));
    }

    public function policies()
    {
        $filterType = 'policy';
        $pageTitle = 'Policy Templates';

        $templates = Template::where('type', $filterType)
            ->latest()
            ->paginate(15);

        return view('admin.pages.templatess.index', compact('templates', 'pageTitle', 'filterType'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:general,offer_letter,experience_letter,policy',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            Template::create([
                'company_id' => null,
                'name' => $data['name'],
                'type' => $data['type'],
                'subject' => $data['subject'] ?? null,
                'content' => $data['body'],
                'is_active' => (bool)($data['is_active'] ?? true),
            ]);

            return back()->with('success', 'Template created successfully.');
        } catch (Throwable $e) {
            Log::error('Template store failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to create template.');
        }
    }

    public function update(Request $request, Template $template)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:general,offer_letter,experience_letter,policy',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $template->update([
                'name' => $data['name'],
                'type' => $data['type'],
                'subject' => $data['subject'] ?? null,
                'body' => $data['body'],
                'is_active' => (bool)($data['is_active'] ?? true),
            ]);

            return back()->with('success', 'Template updated successfully.');
        } catch (Throwable $e) {
            Log::error('Template update failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Unable to update template.');
        }
    }

    public function destroy(Template $template)
    {
        try {
            $template->delete();
            return back()->with('success', 'Template deleted successfully.');
        } catch (Throwable $e) {
            Log::error('Template delete failed', ['message' => $e->getMessage()]);
            return back()->with('error', 'Unable to delete template.');
        }
    }
}
