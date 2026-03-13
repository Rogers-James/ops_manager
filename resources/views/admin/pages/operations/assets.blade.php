@extends('admin.layouts.layout')

@section('title', 'Admin - Asset List')

@section('main-content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Operations / Assets /</span> Assets List</h4>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAsset">
            <i class="bx bx-plus me-1"></i> Add Asset
        </button>
    </div>

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Asset</th>
                        <th>Category</th>
                        <th>Code</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $i => $asset)
                        <tr>
                            <td>{{ $assets->firstItem() + $i }}</td>
                            <td>
                                <strong>{{ $asset->name }}</strong><br>
                                <small class="text-muted">{{ $asset->serial_no ?? '-' }}</small>
                            </td>
                            <td>{{ $asset->category->name ?? '-' }}</td>
                            <td>{{ $asset->asset_code ?? '-' }}</td>
                            <td><span class="badge bg-label-info">{{ strtoupper($asset->status) }}</span></td>
                            <td>
                                @if($asset->activeAssignment?->employee)
                                    {{ $asset->activeAssignment->employee->employee_code }} -
                                    {{ $asset->activeAssignment->employee->first_name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="d-flex gap-1">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editAsset-{{ $asset->id }}">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.assets.destroy', $asset) }}" onsubmit="return confirm('Delete this asset?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editAsset-{{ $asset->id }}" data-bs-backdrop="static" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <form class="modal-content" method="POST" action="{{ route('admin.assets.update', $asset) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Asset</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2">
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label">Category</label>
                                                <select name="asset_category_id" class="form-select" required>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}" {{ $asset->asset_category_id == $cat->id ? 'selected' : '' }}>
                                                            {{ $cat->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label">Name</label>
                                                <input class="form-control" name="name" value="{{ $asset->name }}" required>
                                            </div>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Asset Code</label>
                                                <input class="form-control" name="asset_code" value="{{ $asset->asset_code }}">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Serial No</label>
                                                <input class="form-control" name="serial_no" value="{{ $asset->serial_no }}">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-select" required>
                                                    @foreach(['available','assigned','repair','retired'] as $st)
                                                        <option value="{{ $st }}" {{ $asset->status === $st ? 'selected' : '' }}>
                                                            {{ ucfirst($st) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label">Purchase Date</label>
                                                <input type="date" class="form-control" name="purchase_date"
                                                    value="{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('Y-m-d') : '' }}">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label">Cost</label>
                                                <input type="number" step="0.01" class="form-control" name="cost" value="{{ $asset->cost }}">
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label">Notes</label>
                                            <input class="form-control" name="notes" value="{{ $asset->notes }}">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                                        <button class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No assets found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $assets->links() }}</div>
    </div>
</div>

<div class="modal fade" id="addAsset" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ route('admin.assets.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Category</label>
                        <select name="asset_category_id" class="form-select" required>
                            <option value="">-- Select --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" required>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Asset Code</label>
                        <input class="form-control" name="asset_code">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Serial No</label>
                        <input class="form-control" name="serial_no">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach(['available','assigned','repair','retired'] as $st)
                                <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" class="form-control" name="purchase_date">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Cost</label>
                        <input type="number" step="0.01" class="form-control" name="cost">
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label">Notes</label>
                    <input class="form-control" name="notes">
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
