@extends('admin.layouts.layout')

@section('title', 'Admin - Asset Assignments')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Operations / Assets /</span> Asset Assignments</h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignAsset">
                <i class="bx bx-plus me-1"></i> Assign Asset
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Asset</th>
                            <th>Employee</th>
                            <th>Assigned At</th>
                            <th>Returned At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $i => $a)
                            <tr>
                                <td>{{ $assignments->firstItem() + $i }}</td>
                                <td>{{ $a->asset->name ?? '-' }}</td>
                                <td>{{ $a->employee->employee_code ?? '' }} - {{ $a->employee->first_name ?? '' }}
                                    {{ $a->employee->last_name ?? '' }}</td>
                                <td>{{ \Carbon\Carbon::parse($a->assigned_at)->format('Y-m-d') }}</td>
                                <td>{{ $a->returned_at ? \Carbon\Carbon::parse($a->returned_at)->format('Y-m-d') : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-label-{{ $a->returned_at ? 'secondary' : 'success' }}">
                                        {{ $a->returned_at ? 'Returned' : 'Active' }}
                                    </span>
                                </td>
                                <td class="d-flex gap-1">
                                    @if (!$a->returned_at)
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#returnAsset-{{ $a->id }}">
                                            <i class="bx bx-undo"></i>
                                        </button>
                                    @endif

                                    <form method="POST" action="{{ route('admin.asset_assignments.destroy', $a) }}"
                                        onsubmit="return confirm('Delete this assignment?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="returnAsset-{{ $a->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.asset_assignments.return', $a) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Return Asset</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <strong>{{ $a->asset->name ?? '-' }}</strong> assigned to
                                                <strong>{{ $a->employee->first_name ?? '' }}
                                                    {{ $a->employee->last_name ?? '' }}</strong>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Returned At</label>
                                                <input type="date" class="form-control" name="returned_at" required
                                                    value="{{ date('Y-m-d') }}">
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Return Notes</label>
                                                <input class="form-control" name="return_notes">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary">Return</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No assignments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $assignments->links() }}</div>
        </div>
    </div>

    <div class="modal fade" id="assignAsset" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.asset_assignments.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Asset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Asset</label>
                            <select name="asset_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($assets as $asset)
                                    <option value="{{ $asset->id }}">
                                        {{ $asset->name }} ({{ $asset->asset_code ?? 'No Code' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($employees as $e)
                                    <option value="{{ $e->id }}">
                                        {{ $e->employee_code }} - {{ $e->first_name }} {{ $e->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Assigned At</label>
                        <input type="date" class="form-control" name="assigned_at" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Notes</label>
                        <input class="form-control" name="notes">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
@endsection
