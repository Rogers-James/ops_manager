@extends('admin.layouts.layout')

@section('title', 'Admin - Employee Reports')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold mb-3">
            <span class="text-muted fw-light">Reports /</span> Employee Reports
        </h4>

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.reports.employee') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($employees as $i => $emp)
                            <tr>
                                <td>{{ $employees->firstItem() + $i }}</td>
                                <td><span class="badge bg-label-secondary">{{ $emp->employee_code }}</span></td>
                                <td><strong>{{ $emp->first_name }} {{ $emp->last_name }}</strong></td>
                                <td>{{ $emp->email ?? '-' }}</td>
                                <td>{{ $emp->phone ?? '-' }}</td>
                                <td>{{ $emp->employment->department->name ?? '-' }}</td>
                                <td>{{ $emp->employment->designation->name ?? '-' }}</td>
                                <td>{{ $emp->employment->location->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-label-{{ $emp->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($emp->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">No employee records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
@endsection
