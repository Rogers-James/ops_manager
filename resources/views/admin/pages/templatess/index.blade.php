@extends('admin.layouts.layout')

@section('title', 'Admin - Dashboard')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">Templates /</span> {{ $pageTitle }}
            </h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTemplate">
                <i class="bx bx-plus me-1"></i> Add Template
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($templates as $i => $template)
                            <tr>
                                <td>{{ $templates->firstItem() + $i }}</td>
                                <td><strong>{{ $template->name }}</strong></td>
                                <td>
                                    <span class="badge bg-label-info">
                                        {{ ucwords(str_replace('_', ' ', $template->type)) }}
                                    </span>
                                </td>
                                <td>{{ $template->subject ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $template->is_active ? 'success' : 'secondary' }}">
                                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editTemplate-{{ $template->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.templates.destroy', $template) }}"
                                        onsubmit="return confirm('Delete this template?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No templates found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $templates->links() }}
            </div>
        </div>

    </div>

    {{-- Add Template Modal --}}
    <div class="modal fade" id="addTemplate" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form class="modal-content" method="POST" action="{{ route('admin.templates.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Template Name</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                @php
                                    $types = [
                                        'general' => 'General',
                                        'offer_letter' => 'Offer Letter',
                                        'experience_letter' => 'Experience Letter',
                                        'policy' => 'Policy',
                                    ];
                                @endphp

                                @foreach ($types as $key => $label)
                                    @if (!$filterType || $filterType === $key)
                                        <option value="{{ $key }}"
                                            {{ old('type', $filterType) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Body</label>
                        <textarea name="body" class="form-control" rows="12" required>{{ old('body') }}</textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            {{ old('is_active', 1) ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modals --}}
    @foreach ($templates as $template)
        <div class="modal fade" id="editTemplate-{{ $template->id }}" data-bs-backdrop="static" tabindex="-1"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <form class="modal-content" method="POST" action="{{ route('admin.templates.update', $template) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Template</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Template Name</label>
                                <input type="text" name="name" class="form-control" required
                                    value="{{ $template->name }}">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select" required>
                                    @foreach ([
            'general' => 'General',
            'offer_letter' => 'Offer Letter',
            'experience_letter' => 'Experience Letter',
            'policy' => 'Policy',
        ] as $key => $label)
                                        <option value="{{ $key }}"
                                            {{ $template->type === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" value="{{ $template->subject }}">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Body</label>
                            <textarea name="body" class="form-control" rows="12" required>{{ $template->body }}</textarea>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                {{ $template->is_active ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
