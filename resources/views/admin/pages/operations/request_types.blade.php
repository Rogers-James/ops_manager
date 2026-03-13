@extends('admin.layouts.layout')

@section('title', 'Admin - Reuqest types')

@section('main-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Operations / HR Requests /</span> Request Types</h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRequestType">
                <i class="bx bx-plus me-1"></i> Add Request Type
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Workflow</th>
                            <th>Requires Doc</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requestTypes as $i => $type)
                            <tr>
                                <td>{{ $requestTypes->firstItem() + $i }}</td>
                                <td><strong>{{ $type->name }}</strong></td>
                                <td>{{ $type->code ?? '-' }}</td>
                                <td>{{ $type->workflow->name ?? '-' }}</td>
                                <td>{{ $type->requires_document ? 'Yes' : 'No' }}</td>
                                <td>{{ $type->is_active ? 'Active' : 'Inactive' }}</td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editRequestType-{{ $type->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.request_types.destroy', $type) }}"
                                        onsubmit="return confirm('Delete this request type?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>


                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No request types found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $requestTypes->links() }}</div>
        </div>
    </div>
    @foreach ($requestTypes as $type)
        <div class="modal fade" id="editRequestType-{{ $type->id }}" data-bs-backdrop="static" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form class="modal-content" method="POST" action="{{ route('admin.request_types.update', $type) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Request Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Name</label>
                                <input class="form-control" name="name" value="{{ $type->name }}" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Code</label>
                                <input class="form-control" name="code" value="{{ $type->code }}">
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Workflow</label>
                            <select name="workflow_id" class="form-select">
                                <option value="">-- None --</option>
                                @foreach ($workflows as $wf)
                                    <option value="{{ $wf->id }}"
                                        {{ $type->workflow_id == $wf->id ? 'selected' : '' }}>
                                        {{ $wf->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="requires_document" value="1"
                                {{ $type->requires_document ? 'checked' : '' }}>
                            <label class="form-check-label">Requires document</label>
                        </div>

                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                {{ $type->is_active ? 'checked' : '' }}>
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
    @endforeach

    <div class="modal fade" id="addRequestType" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.request_types.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Request Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Name</label>
                            <input class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Code</label>
                            <input class="form-control" name="code">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Workflow</label>
                        <select name="workflow_id" class="form-select">
                            <option value="">-- None --</option>
                            @foreach ($workflows as $wf)
                                <option value="{{ $wf->id }}">{{ $wf->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="requires_document" value="1">
                        <label class="form-check-label">Requires document</label>
                    </div>

                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
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
@endsection
