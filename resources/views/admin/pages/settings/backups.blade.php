@extends('admin.layouts.layout')

@section('title', 'Admin - Backups')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold mb-3">
            <span class="text-muted fw-light">System Settings /</span> Backups / Exports
        </h4>

        <div class="row g-3">

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Export Employees</h5>
                        <p class="text-muted">Download employee data export.</p>
                        <form method="POST" action="{{ route('admin.settings.exports.employees') }}">
                            @csrf
                            <button class="btn btn-primary">Export Employees</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Export Attendance</h5>
                        <p class="text-muted">Download attendance data export.</p>
                        <form method="POST" action="{{ route('admin.settings.exports.attendance') }}">
                            @csrf
                            <button class="btn btn-primary">Export Attendance</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Export Payroll</h5>
                        <p class="text-muted">Download payroll data export.</p>
                        <form method="POST" action="{{ route('admin.settings.exports.payroll') }}">
                            @csrf
                            <button class="btn btn-primary">Export Payroll</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
