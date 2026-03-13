@extends('admin.layouts.layout')

@section('title', 'Admin - Salary Structures')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Payroll Setup /</span> Salary Structures</h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStructure">
                <i class="bx bx-plus me-1"></i> Add Structure
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Structure</th>
                            <th>Currency</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($structures as $i => $st)
                            <tr>
                                <td>{{ $structures->firstItem() + $i }}</td>
                                <td><strong>{{ $st->name }}</strong></td>
                                <td>{{ $st->currency_code ?? '-' }}</td>
                                <td><span class="badge bg-label-info">{{ $st->items->count() }}</span></td>
                                <td><span
                                        class="badge bg-label-{{ $st->is_active ? 'success' : 'secondary' }}">{{ $st->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#manageStructure-{{ $st->id }}"><i
                                            class="bx bx-cog"></i></button>
                                    <form method="POST" action="{{ route('admin.salary_structures.destroy', $st) }}"
                                        onsubmit="return confirm('Delete this structure?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Manage Modal (edit + items) --}}
                            <div class="modal fade" id="manageStructure-{{ $st->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Manage Structure: {{ $st->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            {{-- Update structure --}}
                                            <form method="POST"
                                                action="{{ route('admin.salary_structures.update', $st) }}" class="mb-3">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="__modal"
                                                    value="manageStructure-{{ $st->id }}">

                                                <div class="row g-2">
                                                    <div class="col-md-5">
                                                        <label class="form-label">Name</label>
                                                        <input class="form-control" name="name"
                                                            value="{{ old('name', $st->name) }}" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Currency</label>
                                                        <input class="form-control" name="currency_code"
                                                            value="{{ old('currency_code', $st->currency_code) }}"
                                                            placeholder="USD">
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox" name="is_active"
                                                                value="1"
                                                                {{ old('is_active', (int) $st->is_active) ? 'checked' : '' }}>
                                                            <label class="form-check-label">Active</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button class="btn btn-primary w-100">Update</button>
                                                    </div>
                                                </div>
                                            </form>

                                            <hr>

                                            {{-- Add Item --}}
                                            <form method="POST"
                                                action="{{ route('admin.salary_structures.items.add', $st) }}"
                                                class="mb-3">
                                                @csrf
                                                <input type="hidden" name="__modal"
                                                    value="manageStructure-{{ $st->id }}">

                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Component</label>
                                                        <select name="salary_component_id" class="form-select" required>
                                                            <option value="">-- Select --</option>
                                                            @foreach ($components as $c)
                                                                <option value="{{ $c->id }}">{{ $c->name }}
                                                                    ({{ $c->type }}, {{ $c->calc_type }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="form-label">Amount</label>
                                                        <input type="number" step="0.01" class="form-control"
                                                            name="amount" placeholder="1000">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label class="form-label">Percent</label>
                                                        <input type="number" step="0.01" class="form-control"
                                                            name="percent" placeholder="10">
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label">Formula</label>
                                                        <input class="form-control" name="formula" placeholder="BASIC*0.1">
                                                    </div>

                                                    <div class="col-md-1">
                                                        <label class="form-label">Order</label>
                                                        <input type="number" class="form-control" name="sort_order"
                                                            value="0">
                                                    </div>

                                                    <div class="col-md-12 d-flex justify-content-end">
                                                        <button class="btn btn-primary btn-sm">Add Component</button>
                                                    </div>
                                                </div>
                                            </form>

                                            {{-- Items List --}}
                                            <div class="table-responsive text-nowrap">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Component</th>
                                                            <th>Type</th>
                                                            <th>Calc</th>
                                                            <th>Amount</th>
                                                            <th>Percent</th>
                                                            <th>Formula</th>
                                                            <th>Order</th>
                                                            <th>Remove</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($st->items->sortBy('sort_order') as $item)
                                                            <tr>
                                                                <td>{{ $item->component->name ?? '-' }}</td>
                                                                <td>{{ $item->component->type ?? '-' }}</td>
                                                                <td>{{ $item->component->calc_type ?? '-' }}</td>
                                                                <td>{{ $item->amount ?? '-' }}</td>
                                                                <td>{{ $item->percent ?? '-' }}</td>
                                                                <td>{{ $item->formula ?? '-' }}</td>
                                                                <td>{{ $item->sort_order ?? 0 }}</td>
                                                                <td>
                                                                    <form method="POST"
                                                                        action="{{ route('admin.salary_structures.items.remove', $item) }}"
                                                                        onsubmit="return confirm('Remove this item?')">
                                                                        @csrf @method('DELETE')
                                                                        <button class="btn btn-sm btn-danger"><i
                                                                                class="bx bx-trash"></i></button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="8" class="text-center py-3">No items yet.
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No structures found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $structures->links() }}</div>
        </div>
    </div>

    {{-- Add Structure --}}
    <div class="modal fade" id="addStructure" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.salary_structures.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addStructure">

                <div class="modal-header">
                    <h5 class="modal-title">Add Salary Structure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-8 mb-2">
                            <label class="form-label">Name</label>
                            <input class="form-control" name="name" required value="{{ old('name') }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Currency</label>
                            <input class="form-control" name="currency_code" value="{{ old('currency_code') }}"
                                placeholder="USD">
                        </div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                            {{ old('is_active', 1) ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>

                    @if ($errors->any() && old('__modal') === 'addStructure')
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
