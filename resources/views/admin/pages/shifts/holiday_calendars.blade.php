@extends('admin.layouts.layout')

@section('title', 'Admin - Holiday Calendars')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Shifts & Calendar /</span> Holiday Calendars</h4>

            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCalendar">
                <i class="bx bx-plus me-1"></i> Add Calendar
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($calendars as $i => $c)
                            <tr>
                                <td>{{ $calendars->firstItem() + $i }}</td>
                                <td><strong>{{ $c->name }}</strong></td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editCalendar-{{ $c->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    <form method="POST" action="{{ route('admin.holiday_calendars.destroy', $c) }}"
                                        onsubmit="return confirm('Delete this calendar?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="editCalendar-{{ $c->id }}" data-bs-backdrop="static"
                                tabindex="-1">
                                <div class="modal-dialog">
                                    <form class="modal-content" method="POST"
                                        action="{{ route('admin.holiday_calendars.update', $c) }}">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="__modal" value="editCalendar-{{ $c->id }}">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Calendar</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <label class="form-label">Name</label>
                                            <input class="form-control" name="name" value="{{ old('name', $c->name) }}"
                                                required>

                                            @if ($errors->any() && old('__modal') === "editCalendar-$c->id")
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
                                <td colspan="3" class="text-center py-4">No calendars found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $calendars->links() }}</div>
        </div>
    </div>

    <div class="modal fade" id="addCalendar" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.holiday_calendars.store') }}">
                @csrf
                <input type="hidden" name="__modal" value="addCalendar">

                <div class="modal-header">
                    <h5 class="modal-title">Add Calendar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Name</label>
                    <input class="form-control" name="name" value="{{ old('name') }}" required>

                    @if ($errors->any() && old('__modal') === 'addCalendar')
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
