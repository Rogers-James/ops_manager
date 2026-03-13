@extends('admin.layouts.layout')

@section('title', 'Admin - Profile')

@section('main-content')

    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Account Settings /</span> Account</h4>

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <h5 class="card-header">Profile Details</h5>

                        {{-- PHOTO --}}
                        <div class="card-body">
                            <div class="d-flex align-items-start align-items-sm-center gap-4">
                                <img src="{{ $employee->photo_path ? asset('storage/' . $employee->photo_path) : asset('admin-assets/assets/img/avatars/1.png') }}"
                                    alt="employee-avatar" class="d-block rounded" height="100" width="100">

                                @if ($canUploadPhoto ?? false)
                                    <form method="POST" action="{{ route('admin.employees.photo.update', $employee->id) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="button-wrapper">
                                            <label class="btn btn-primary me-2 mb-2" tabindex="0">
                                                <span class="d-none d-sm-block">Upload new photo</span>
                                                <i class="bx bx-upload d-block d-sm-none"></i>
                                                <input type="file" name="photo" hidden accept="image/png, image/jpeg">
                                            </label>

                                            <button type="submit" class="btn btn-outline-secondary mb-2">
                                                Save Photo
                                            </button>

                                            <p class="text-muted mb-0">JPG/PNG only. Max 800KB</p>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <hr class="my-0">
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.employees.profile.update', $employee->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    {{-- Basic --}}
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">First Name</label>
                                        <input class="form-control" type="text" name="first_name"
                                            value="{{ old('first_name', $employee->first_name) }}" required>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Last Name</label>
                                        <input class="form-control" type="text" name="last_name"
                                            value="{{ old('last_name', $employee->last_name) }}">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">E-mail</label>
                                        <input class="form-control" type="email" name="email"
                                            value="{{ old('email', $employee->email) }}">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input class="form-control" type="text" name="phone"
                                            value="{{ old('phone', $employee->phone) }}">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">DOB</label>
                                        <input class="form-control" type="date" name="dob"
                                            value="{{ old('dob', $employee->dob) }}">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Gender</label>
                                        <select name="gender" class="form-select">
                                            <option value="">-- Select --</option>
                                            @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $k => $v)
                                                <option value="{{ $k }}"
                                                    {{ old('gender', $employee->gender) === $k ? 'selected' : '' }}>
                                                    {{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Marital Status</label>
                                        <select name="marital_status" class="form-select">
                                            <option value="">-- Select --</option>
                                            @foreach (['single' => 'Single', 'married' => 'Married', 'divorced' => 'Divorced', 'widowed' => 'Widowed'] as $k => $v)
                                                <option value="{{ $k }}"
                                                    {{ old('marital_status', $employee->marital_status) === $k ? 'selected' : '' }}>
                                                    {{ $v }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Status: Admin/HR only --}}
                                    @if ($canEditStatus ?? false)
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="form-select" required>
                                                <option value="active"
                                                    {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive"
                                                    {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                {{-- EMPLOYMENT: Admin/HR only --}}
                                @if ($canEditEmployment ?? false)
                                    <hr class="my-3">

                                    <h6 class="mb-3">Employment</h6>

                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Joining Date</label>
                                            <input type="date" name="joining_date" class="form-control"
                                                value="{{ old('joining_date', optional($employee->employment)->joining_date) }}">
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Employment Type</label>
                                            <select name="employment_type" class="form-select">
                                                @php
                                                    $types = [
                                                        'full_time',
                                                        'part_time',
                                                        'contract',
                                                        'intern',
                                                        'daily_wage',
                                                    ];
                                                    $sel = old(
                                                        'employment_type',
                                                        optional($employee->employment)->employment_type ?? 'full_time',
                                                    );
                                                @endphp
                                                @foreach ($types as $t)
                                                    <option value="{{ $t }}"
                                                        {{ $sel === $t ? 'selected' : '' }}>
                                                        {{ ucwords(str_replace('_', ' ', $t)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Department</label>
                                            <select name="department_id" class="form-select">
                                                <option value="">-- Select --</option>
                                                @foreach ($departments as $dep)
                                                    <option value="{{ $dep->id }}"
                                                        {{ (string) old('department_id', optional($employee->employment)->department_id) === (string) $dep->id ? 'selected' : '' }}>
                                                        {{ $dep->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Designation</label>
                                            <select name="designation_id" class="form-select">
                                                <option value="">-- Select --</option>
                                                @foreach ($designations as $des)
                                                    <option value="{{ $des->id }}"
                                                        {{ (string) old('designation_id', optional($employee->employment)->designation_id) === (string) $des->id ? 'selected' : '' }}>
                                                        {{ $des->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Location</label>
                                            <select name="location_id" class="form-select">
                                                <option value="">-- Select --</option>
                                                @foreach ($locations as $loc)
                                                    <option value="{{ $loc->id }}"
                                                        {{ (string) old('location_id', optional($employee->employment)->location_id) === (string) $loc->id ? 'selected' : '' }}>
                                                        {{ $loc->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Grade</label>
                                            <select name="grade_id" class="form-select">
                                                <option value="">-- Select --</option>
                                                @foreach ($grades as $g)
                                                    <option value="{{ $g->id }}"
                                                        {{ (string) old('grade_id', optional($employee->employment)->grade_id) === (string) $g->id ? 'selected' : '' }}>
                                                        {{ $g->name }}
                                                        {{ $g->rank ? '(Rank ' . $g->rank . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Manager</label>
                                            <select name="manager_id" class="form-select">
                                                <option value="">-- Select --</option>
                                                @foreach ($managers as $m)
                                                    <option value="{{ $m->id }}"
                                                        {{ (string) old('manager_id', optional($employee->employment)->manager_id) === (string) $m->id ? 'selected' : '' }}>
                                                        {{ $m->employee_code }} - {{ $m->first_name }}
                                                        {{ $m->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- cost center only if you want now --}}
                                        @if (isset($costCenters))
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Cost Center</label>
                                                <select name="cost_center_id" class="form-select">
                                                    <option value="">-- Select --</option>
                                                    @foreach ($costCenters as $cc)
                                                        <option value="{{ $cc->id }}"
                                                            {{ (string) old('cost_center_id', optional($employee->employment)->cost_center_id) === (string) $cc->id ? 'selected' : '' }}>
                                                            {{ $cc->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary me-2">Save changes</button>
                                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- / Content -->
    </div>

@endsection
