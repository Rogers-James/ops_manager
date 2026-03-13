@extends('admin.layouts.layout')

@section('title', 'Admin - Employees')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">HR /</span> Employees
        </h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Employees</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEmployee">
                    <i class="bx bx-plus me-1"></i> Add Employee
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Designation</th>
                            <th>Join Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($employees as $i => $emp)
                            <tr>
                                <td>{{ $employees->firstItem() + $i }}</td>

                                <td>
                                    <span class="badge bg-label-secondary">
                                        {{ $emp->employee_code ?? '-' }}
                                    </span>
                                </td>

                                <td><strong>{{ $emp->first_name }} {{ $emp->last_name }}</strong></td>
                                <td>{{ $emp->email ?? '-' }}</td>
                                <td>{{ $emp->phone ?? '-' }}</td>

                                <td>{{ $emp->designation ?? '-' }}</td>

                                <td>
                                    {{ optional($emp->employment)->joining_date
                                        ? \Carbon\Carbon::parse($emp->employment->joining_date)->format('Y-m-d')
                                        : '-' }}
                                </td>

                                <td>
                                    @if ($emp->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('admin.profile.get', $emp->id) }}">
                                        <span class="badge bg-label-info">Edit</span>
                                    </a>
                                    <a href="{{ route('admin.employees.documents', $emp->id) }}">
                                        <span class="badge bg-label-primary">Docs</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No employees found.</td>
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

    {{-- Add Employee Modal --}}
    <div class="modal fade" id="addEmployee" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form class="modal-content" method="POST" action="{{ route('admin.employees.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Employee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- Basic info (employees table) --}}
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" required
                                value="{{ old('first_name') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">DOB</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Marital Status</label>
                            <select name="marital_status" class="form-select">
                                <option value="">-- Select --</option>
                                <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single
                                </option>
                                <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married
                                </option>
                                <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>
                                    Divorced
                                </option>
                                <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed
                                </option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- Employment info (employee_employments table) --}}
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Join Date</label>
                            <input type="date" name="joining_date" class="form-control" required
                                value="{{ old('joining_date', date('Y-m-d')) }}">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Employment Type</label>
                            <select name="employment_type" class="form-select" required>
                                @php
                                    $types = ['full_time', 'part_time', 'contract', 'intern', 'daily_wage'];
                                    $selected = old('employment_type', 'full_time');
                                @endphp

                                @foreach ($types as $t)
                                    <option value="{{ $t }}" {{ $selected === $t ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $t)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- You can add department/designation later if you have those tables --}}
                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Location</label>
                            <select name="location_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}"
                                        {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($departments as $dep)
                                    <option value="{{ $dep->id }}"
                                        {{ old('department_id') == $dep->id ? 'selected' : '' }}>
                                        {{ $dep->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Designation</label>
                            <select name="designation_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($designations as $des)
                                    <option value="{{ $des->id }}"
                                        {{ old('designation_id') == $des->id ? 'selected' : '' }}>
                                        {{ $des->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Manager</label>
                            <select name="manager_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($managers as $m)
                                    <option value="{{ $m->id }}"
                                        {{ old('manager_id') == $m->id ? 'selected' : '' }}>
                                        {{ $m->employee_code }} - {{ $m->first_name }} {{ $m->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Grade</label>
                            <select name="grade_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($grades as $g)
                                    <option value="{{ $g->id }}"
                                        {{ old('grade_id') == $g->id ? 'selected' : '' }}>
                                        {{ $g->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Cost Center</label>
                            <select name="cost_center_id" class="form-select">
                                <option value="">-- Select --</option>
                                @foreach ($costCenters as $cc)
                                    <option value="{{ $cc->id }}"
                                        {{ old('cost_center_id') == $cc->id ? 'selected' : '' }}>
                                        {{ $cc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reopen modal if validation errors --}}
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                new bootstrap.Modal(document.getElementById('addEmployee')).show();
            });
        </script>
    @endif

@endsection
