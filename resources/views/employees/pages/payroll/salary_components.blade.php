@extends('admin.layouts.layout')

@section('title', 'Admin - Salary Components')

@section('main-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Payroll Setup /</span> Salary Components</h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addComponent">
                <i class="bx bx-plus me-1"></i> Add Component
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
                            <th>Type</th>
                            <th>Calc</th>
                            <th>Taxable</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($components as $i => $c)
                            <tr>
                                <td>{{ $components->firstItem() + $i }}</td>
                                <td><strong>{{ $c->name }}</strong></td>
                                <td>{{ $c->code ?? '-' }}</td>
                                <td><span
                                        class="badge bg-label-{{ $c->type === 'earning' ? 'success' : 'danger' }}">{{ strtoupper($c->type) }}</span>
                                </td>
                                <td><span class="badge bg-label-info">{{ strtoupper($c->calc_type) }}</span></td>
                                <td>{{ $c->is_taxable ? 'Yes' : 'No' }}</td>
                                <td><span
                                        class="badge bg-label-{{ $c->is_active ? 'success' : 'secondary' }}">{{ $c->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editComponent-{{ $c->id }}"><i
                                            class="bx bx-edit-alt"></i></button>
                                    <form method="POST" action="{{ route('admin.salary_components.destroy', $c) }}"
                                        onsubmit="return confirm('Delete this component?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit --}}
                            <div class="modal fade" id="editComponent-{{ $c->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.salary_components.update', $c) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="__modal" value="editComponent-{{ $c->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Component</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="row g-2">
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Name</label>
                                                    <input class="form-control" name="name" required
                                                        value="{{ old('name', $c->name) }}">
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <label class="form-label">Code</label>
                                                    <input class="form-control" name="code"
                                                        value="{{ old('code', $c->code) }}">
                                                </div>
                                            </div>

                                            <div class="row g-2">
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Type</label>
                                                    <select name="type" class="form-select" required>
                                                        @foreach (['earning', 'deduction'] as $t)
                                                            <option value="{{ $t }}"
                                                                {{ old('type', $c->type) === $t ? 'selected' : '' }}>
                                                                {{ ucwords($t) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Calc Type</label>
                                                    <select name="calc_type" class="form-select" required>
                                                        @foreach (['fixed', 'percent', 'formula'] as $t)
                                                            <option value="{{ $t }}"
                                                                {{ old('calc_type', $c->calc_type) === $t ? 'selected' : '' }}>
                                                                {{ ucwords($t) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label">Options</label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_taxable"
                                                            value="1"
                                                            {{ old('is_taxable', (int) $c->is_taxable) ? 'checked' : '' }}>
                                                        <label class="form-check-label">Taxable</label>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active"
                                                            value="1"
                                                            {{ old('is_active', (int) $c->is_active) ? 'checked' : '' }}>
                                                        <label class="form-check-label">Active</label>
                                                    </div>
                                                </div>
                                            </div>

                                            @if ($errors->any() && old('__modal') === "editComponent-$c->id")
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
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No components found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $components->links() }}</div>
        </div>
    </div>

    {{-- Add --}}
    <div class="modal fade" id="addComponent" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.salary_components.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addComponent">

                <div class="modal-header">
                    <h5 class="modal-title">Add Salary Component</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Name</label>
                            <input class="form-control" name="name" required value="{{ old('name') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Code</label>
                            <input class="form-control" name="code" value="{{ old('code') }}">
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="earning" {{ old('type', 'earning') === 'earning' ? 'selected' : '' }}>Earning
                                </option>
                                <option value="deduction" {{ old('type') === 'deduction' ? 'selected' : '' }}>Deduction</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Calc Type</label>
                            <select name="calc_type" class="form-select" required>
                                @foreach (['fixed', 'percent', 'formula'] as $t)
                                    <option value="{{ $t }}"
                                        {{ old('calc_type', 'fixed') === $t ? 'selected' : '' }}>{{ ucwords($t) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Options</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_taxable" value="1"
                                    {{ old('is_taxable') ? 'checked' : '' }}>
                                <label class="form-check-label">Taxable</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>

                    @if ($errors->any() && old('__modal') === 'addComponent')
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
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if ($errors->any() && old('__modal'))
                new bootstrap.Modal(document.getElementById(@json(old('__modal')))).show();
            @endif
        });
    </script>
@endsection
