@extends('admin.layouts.layout')

@section('title', 'Admin - Documents')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Employees /</span> Documents
        </h4>

        <div class="card mb-3">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1">{{ $employee->first_name }} {{ $employee->last_name }}</h5>
                    <small class="text-muted">{{ $employee->employee_code }}</small>
                </div>

                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDoc">
                    <i class="bx bx-upload me-1"></i> Upload Document
                </button>
            </div>
        </div>

        {{-- Missing required docs --}}
        @if (isset($missingRequired) && $missingRequired->count())
            <div class="alert alert-warning">
                <strong>Missing required documents:</strong>
                @foreach ($missingRequired as $t)
                    <span class="badge bg-label-warning">{{ $t->name }}</span>
                @endforeach
            </div>
        @endif

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Expiry</th>
                            <th>File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($documents as $i => $doc)
                            @php
                                $exp = $doc->expires_at ? \Carbon\Carbon::parse($doc->expires_at) : null;
                                $ext = $doc->file_path ? strtoupper(pathinfo($doc->file_path, PATHINFO_EXTENSION)) : '';
                                $orig = $doc->meta['original_name'] ?? null;
                            @endphp

                            <tr>
                                <td>{{ $documents->firstItem() + $i }}</td>

                                <td>
                                    <strong>{{ $doc->type->name ?? 'N/A' }}</strong>
                                    @if ($doc->type->required ?? false)
                                        <span class="badge bg-label-danger ms-1">Required</span>
                                    @endif
                                </td>

                                <td>{{ $doc->title }}</td>

                                <td>
                                    @if ($exp)
                                        <span class="badge {{ $exp->isPast() ? 'bg-label-danger' : 'bg-label-success' }}">
                                            {{ $exp->format('Y-m-d') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-label-secondary">{{ $ext ?: 'FILE' }}</span>
                                    @if ($orig)
                                        <small class="text-muted d-block">{{ $orig }}</small>
                                    @endif
                                </td>

                                <td>
                                    <div>
                                        <a class="dropdown-item" href="{{ route('admin.documents.download', $doc->id) }}">
                                            <small class="text-muted d-block"> <i class="bx bx-download me-1"></i>
                                                Download</small>
                                        </a>
                                        <form method="POST" action="{{ route('admin.documents.destroy', $doc->id) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"
                                                onclick="return confirm('Delete this document?')">
                                                <i class="bx bx-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No documents uploaded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $documents->links() }}
            </div>
        </div>
    </div>

    {{-- Upload Modal --}}
    <div class="modal fade" id="uploadDoc" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST"
                action="{{ route('admin.employees.documents.store', $employee->id) }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Document Type</label>
                        <select name="document_type_id" class="form-select" required>
                            <option value="">-- Select --</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}"
                                    {{ (string) old('document_type_id') === (string) $t->id ? 'selected' : '' }}>
                                    {{ $t->name }} {{ $t->required ? '(Required)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Title (optional)</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                            placeholder="e.g. CNIC Front">
                        <small class="text-muted">If empty, it will use Document Type name.</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Expiry Date (optional)</label>
                        <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">File (PDF/JPG/PNG)</label>
                        <input type="file" name="file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Max 2MB.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reopen modal on validation errors --}}
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                new bootstrap.Modal(document.getElementById('uploadDoc')).show();
            });
        </script>
    @endif

@endsection
