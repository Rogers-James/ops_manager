@extends('admin.layouts.layout')

@section('title', 'Admin - Run Payroll')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold mb-3"><span class="text-muted fw-light">Payroll Processing /</span> Run Payroll</h4>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.payroll.run.store') }}">
                    @csrf

                    <div class="row g-2">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Pay Schedule</label>
                            <select name="pay_schedule_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($schedules as $s)
                                    <option value="{{ $s->id }}" {{ old('pay_schedule_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }} ({{ $s->frequency }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Period Start</label>
                            <input type="date" class="form-control" name="period_start" required
                                value="{{ old('period_start') }}">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Period End</label>
                            <input type="date" class="form-control" name="period_end" required
                                value="{{ old('period_end') }}">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Notes</label>
                        <input class="form-control" name="notes" value="{{ old('notes') }}">
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger mt-2 mb-0">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-3">
                        <button class="btn btn-primary">
                            <i class="bx bx-play me-1"></i> Generate Payroll Run
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
