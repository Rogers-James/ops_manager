@extends('admin.layouts.layout')

@section('title', 'Admin - Custom Fields')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0">
                <span class="text-muted fw-light">System Settings /</span> Custom Fields
            </h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCustomField">
                <i class="bx bx-plus me-1"></i> Add Field
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Module</th>
                            <th>Label</th>
                            <th>Type</th>
                            <th>Required</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($fields as $i => $field)
                            <tr>
                                <td>{{ $fields->firstItem() + $i }}</td>
                                <td>{{ ucfirst($field->module) }}</td>
                                <td><strong>{{ $field->label }}</strong></td>
                                <td>{{ ucfirst($field->field_type) }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $field->is_required ? 'warning' : 'secondary' }}">
                                        {{ $field->is_required ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST"
                                        action="{{ route('admin.settings.custom_fields.destroy', $field) }}"
                                        onsubmit="return confirm('Delete this field?')">
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
                                <td colspan="6" class="text-center py-4">No custom fields found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $fields->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCustomField" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.settings.custom_fields.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Custom Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Module</label>
                        <select name="module" class="form-select" required>
                            <option value="employee">Employee</option>
                            <option value="attendance">Attendance</option>
                            <option value="leave">Leave</option>
                            <option value="payroll">Payroll</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Label</label>
                        <input type="text" name="label" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Field Type</label>
                        <select name="field_type" class="form-select" required>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="select">Select</option>
                            <option value="textarea">Textarea</option>
                        </select>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_required" value="1">
                        <label class="form-check-label">Required</label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
