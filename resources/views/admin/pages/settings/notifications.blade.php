@extends('admin.layouts.layout')

@section('title', 'Admin - Notifications')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold mb-3">
            <span class="text-muted fw-light">System Settings /</span> Notifications
        </h4>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.notifications.save') }}">
                    @csrf

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="email_notifications" value="1" checked>
                        <label class="form-check-label">Enable Email Notifications</label>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="sms_notifications" value="1">
                        <label class="form-check-label">Enable SMS Notifications</label>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="leave_alerts" value="1" checked>
                        <label class="form-check-label">Leave Request Alerts</label>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="attendance_alerts" value="1">
                        <label class="form-check-label">Attendance Alerts</label>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary">Save Notifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
