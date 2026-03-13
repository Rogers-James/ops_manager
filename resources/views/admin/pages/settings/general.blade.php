@extends('admin.layouts.layout')

@section('title', 'Admin - General')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold mb-3">
            <span class="text-muted fw-light">System Settings /</span> General Settings
        </h4>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.general.save') }}">
                    @csrf

                    <div class="row g-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $company->name ?? '') }}" required>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="form-label">Timezone</label>
                            <input type="text" name="timezone" class="form-control"
                                value="{{ old('timezone', $company->timezone ?? '') }}">
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-4 mb-2">
                            <label class="form-label">Currency Code</label>
                            <input type="text" name="currency_code" class="form-control"
                                value="{{ old('currency_code', $company->currency_code ?? '') }}">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Date Format</label>
                            <input type="text" name="date_format" class="form-control"
                                value="{{ old('date_format', $company->date_format ?? '') }}" placeholder="Y-m-d">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label">Logo Path</label>
                            <input type="text" name="logo_path" class="form-control"
                                value="{{ old('logo_path', $company->logo_path ?? '') }}">
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
