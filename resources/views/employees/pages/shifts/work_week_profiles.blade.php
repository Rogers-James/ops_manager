@extends('admin.layouts.layout')

@section('title', 'Admin - Work Week Profiles')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Shifts & Calendar /</span> Work Week Profiles</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addWorkWeek">
                <i class="bx bx-plus me-1"></i> Add Profile
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Profile</th>
                            <th>Working Days</th>
                            <th>Default</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($profiles as $i => $p)
                            @php
                                $days = [
                                    'Mon' => $p->mon,
                                    'Tue' => $p->tue,
                                    'Wed' => $p->wed,
                                    'Thu' => $p->thu,
                                    'Fri' => $p->fri,
                                    'Sat' => $p->sat,
                                    'Sun' => $p->sun,
                                ];
                            @endphp
                            <tr>
                                <td>{{ $profiles->firstItem() + $i }}</td>
                                <td><strong>{{ $p->name }}</strong></td>
                                <td>
                                    @foreach ($days as $label => $isWorking)
                                        <span
                                            class="badge bg-label-{{ $isWorking ? 'success' : 'secondary' }} me-1">{{ $label }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($p->is_default)
                                        <span class="badge bg-label-primary">DEFAULT</span>
                                    @else
                                        <span class="badge bg-label-secondary">-</span>
                                    @endif
                                </td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editWorkWeek-{{ $p->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.work_week_profiles.destroy', $p) }}"
                                        onsubmit="return confirm('Delete this profile?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editWorkWeek-{{ $p->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.work_week_profiles.update', $p) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="__modal" value="editWorkWeek-{{ $p->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Work Week Profile</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label">Name</label>
                                                <input class="form-control" name="name"
                                                    value="{{ old('name', $p->name) }}" required>
                                            </div>

                                            <div class="row g-2">
                                                @foreach (['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'] as $k => $lbl)
                                                    <div class="col-md-3">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="{{ $k }}" value="1"
                                                                {{ old($k, (int) $p->$k) ? 'checked' : '' }}>
                                                            <label class="form-check-label">{{ $lbl }}
                                                                Working</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <hr>

                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="is_default"
                                                    value="1"
                                                    {{ old('is_default', (int) $p->is_default) ? 'checked' : '' }}>
                                                <label class="form-check-label">Set as default profile</label>
                                            </div>

                                            @if ($errors->any() && old('__modal') === "editWorkWeek-$p->id")
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
                                <td colspan="5" class="text-center py-4">No profiles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $profiles->links() }}</div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addWorkWeek" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="{{ route('admin.work_week_profiles.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addWorkWeek">

                <div class="modal-header">
                    <h5 class="modal-title">Add Work Week Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>

                    <div class="row g-2">
                        @foreach (['mon' => 'Mon', 'tue' => 'Tue', 'wed' => 'Wed', 'thu' => 'Thu', 'fri' => 'Fri', 'sat' => 'Sat', 'sun' => 'Sun'] as $k => $lbl)
                            <div class="col-md-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="{{ $k }}"
                                        value="1"
                                        {{ old($k, in_array($k, ['mon', 'tue', 'wed', 'thu', 'fri']) ? 1 : 0) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $lbl }} Working</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <hr>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_default" value="1"
                            {{ old('is_default') ? 'checked' : '' }}>
                        <label class="form-check-label">Set as default profile</label>
                    </div>

                    @if ($errors->any() && old('__modal') === 'addWorkWeek')
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
