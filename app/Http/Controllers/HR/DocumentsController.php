<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Employee;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class DocumentsController extends Controller
{
    public function index(Employee $employee)
    {
        $employee->load('employment');

        $documents = $employee->documents()
            ->with('type:id,name,required')
            ->latest()
            ->paginate(15);

        // Types for dropdown
        // $companyId = Auth::user()->company_id ?? 1;
        $types = DocumentType::orderBy('name')
            ->get(['id', 'name', 'required']);

        // Required document checklist (optional but very useful)
        $requiredTypes = $types->where('required', 1);
        $uploadedTypeIds = $documents->pluck('document_type_id')->unique()->filter();
        $missingRequired = $requiredTypes->whereNotIn('id', $uploadedTypeIds->all());

        return view('admin.pages.employees.docs', compact(
            'employee',
            'documents',
            'types',
            'missingRequired'
        ));
    }

    public function store(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'title'            => 'nullable|string|max:150',
            'expires_at'       => 'nullable|date',
            'file'             => 'required|file|max:2048|mimes:pdf,jpg,jpeg,png',
        ]);

        try {
            $type = DocumentType::find($data['document_type_id']);
            if (!$type) {
                return back()->withInput()->with('error', 'Invalid document type.');
            }

            $file = $request->file('file');

            // Use employee_code if available (nice folder), fallback to id
            $empFolder = $employee->employee_code ?: ('EMP-' . $employee->id);

            // Nice type folder: "cnic-front", "employment-contract"
            $typeFolder = Str::slug($type->name);

            // Safe unique filename
            $ext = $file->getClientOriginalExtension();
            $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $filename = time() . '_' . $safeName . '.' . $ext;
            // Store in: storage/app/public/employees/{EMP-000001}/documents/{type}/{filename}
            $path = $file->storeAs("employees/{$empFolder}/documents/{$typeFolder}", $filename, 'public');

            $title = $data['title'] ?: $type->name;

            $employee->documents()->create([
                'document_type_id' => $type->id,
                'title'            => $title,
                'file_path'        => $path,
                'expires_at'       => $data['expires_at'] ?? null,
                'meta'             => [
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                    'size'          => $file->getSize(),
                    'stored_name'   => $filename,
                    'type_name'     => $type->name,
                ],
            ]);

            return back()->with('success', 'Document uploaded successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Employee document upload DB error', [
                'message' => $e->getMessage(),
                'errorInfo' => $e->errorInfo,
            ]);
            return back()->withInput()->with('error', 'Database error while uploading document.');
        } catch (\Throwable $e) {
            Log::error('Employee document upload error', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Something went wrong while uploading document.');
        }
    }

    public function download(Document $document)
    {
        // Security: only employee docs here
        if ($document->owner_type !== Employee::class) {
            abort(403);
        }

        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        $filename = $document->meta['original_name'] ?? basename($document->file_path);
        return Storage::disk('public')->download($document->file_path, $filename);
    }

    public function destroy(Document $document)
    {
        try {
            if ($document->owner_type !== Employee::class) {
                abort(403);
            }

            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            return back()->with('success', 'Document deleted successfully!');
        } catch (Throwable $e) {
            Log::error('Employee document delete error', ['message' => $e->getMessage()]);
            return back()->with('error', 'Something went wrong while deleting document.');
        }
    }
}
