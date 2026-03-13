@extends('admin.layouts.layout')

@section('title', 'Admin - Cost Centers')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Company /</span> Cost Centers
        </h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Cost Centers</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCostCenter">
                    <i class="bx bx-plus me-1"></i> Add Cost Center
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($costCenters as $i => $cc)
                            <tr>
                                <td>{{ $costCenters->firstItem() + $i }}</td>
                                <td><strong>{{ $cc->name }}</strong></td>
                                <td>
                                    @if ($cc->code)
                                        <span class="badge bg-label-secondary">{{ $cc->code }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            {{-- Edit --}}
                                            <button class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#editCostCenter" data-id="{{ $cc->id }}"
                                                data-name="{{ $cc->name }}" data-code="{{ $cc->code }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </button>

                                            {{-- Delete --}}
                                            <form method="POST"
                                                action="{{ route('admin.cost_centers.destroy', $cc->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="dropdown-item text-danger"
                                                    onclick="return confirm('Delete this cost center?')">
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No cost centers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $costCenters->links() }}
            </div>
        </div>
    </div>

    {{-- Add Cost Center Modal --}}
    <div class="modal fade" id="addCostCenter" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.cost_centers.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Cost Center</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input class="form-control" type="text" name="name" required value="{{ old('name') }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Code (optional)</label>
                        <input class="form-control" type="text" name="code" value="{{ old('code') }}"
                            placeholder="e.g. CC-001">
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger mt-2 mb-0">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Cost Center Modal --}}
    <div class="modal fade" id="editCostCenter" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" id="editCostCenterForm">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Cost Center</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input class="form-control" type="text" name="name" id="editCostCenterName" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Code (optional)</label>
                        <input class="form-control" type="text" name="code" id="editCostCenterCode"
                            placeholder="e.g. CC-001">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('editCostCenter');

            modal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;

                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                const code = btn.getAttribute('data-code');

                document.getElementById('editCostCenterName').value = name || '';
                document.getElementById('editCostCenterCode').value = code || '';

                document.getElementById('editCostCenterForm').action =
                    "{{ url('/admin/cost-centers') }}/" + id;
            });

            // Reopen ADD modal after validation errors (optional)
            @if ($errors->any())
                new bootstrap.Modal(document.getElementById('addCostCenter')).show();
            @endif
        });
    </script>

@endsection
