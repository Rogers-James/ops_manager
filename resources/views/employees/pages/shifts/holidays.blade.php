@extends('admin.layouts.layout')

@section('title', 'Admin - Holidays')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Shifts & Calendar /</span> Holidays</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHoliday">
                <i class="bx bx-plus me-1"></i> Add Holiday
            </button>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <form class="row g-2 align-items-end" method="GET" action="{{ route('admin.holidays.index') }}">
                    <div class="col-md-4">
                        <label class="form-label">Calendar</label>
                        <select class="form-select" name="holiday_calendar_id">
                            <option value="">All</option>
                            @foreach ($calendars as $c)
                                <option value="{{ $c->id }}"
                                    {{ (string) $calendarId === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filter</button>
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
                            <th>Date</th>
                            <th>Holiday</th>
                            <th>Calendar</th>
                            <th>Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($holidays as $i => $h)
                            <tr>
                                <td>{{ $holidays->firstItem() + $i }}</td>
                                <td><strong>{{ $h->date }}</strong></td>
                                <td>{{ $h->name }}</td>
                                <td>{{ $h->calendar?->name ?? '-' }}</td>
                                <td>
                                    @if ($h->is_paid ?? true)
                                        <span class="badge bg-label-success">PAID</span>
                                    @else
                                        <span class="badge bg-label-secondary">UNPAID</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.holidays.destroy', $h) }}"
                                        onsubmit="return confirm('Delete this holiday?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No holidays found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $holidays->links() }}</div>
        </div>
    </div>

    {{-- Add Holiday Modal --}}
    <div class="modal fade" id="addHoliday" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.holidays.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addHoliday">

                <div class="modal-header">
                    <h5 class="modal-title">Add Holiday</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Calendar</label>
                        <select class="form-select" name="holiday_calendar_id" required>
                            <option value="">-- Select --</option>
                            @foreach ($calendars as $c)
                                <option value="{{ $c->id }}"
                                    {{ (string) old('holiday_calendar_id', $calendarId) === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Date</label>
                            <input class="form-control" type="date" name="date" value="{{ old('date') }}" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Paid?</label>
                            <select class="form-select" name="is_paid">
                                <option value="1" {{ old('is_paid', '1') == '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('is_paid') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>

                    @if ($errors->any() && old('__modal') === 'addHoliday')
                        <div class="alert alert-danger mb-0">
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
