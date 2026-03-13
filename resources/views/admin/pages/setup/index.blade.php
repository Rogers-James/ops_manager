@extends('admin.layouts.layout')

@section('title', 'Admin - Company')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Settings /</span> Company Profile
        </h4>

        <div class="card mb-4">
            <h5 class="card-header">Company Logo</h5>

            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                    <img src="{{ $company->logo_path ? asset('storage/' . $company->logo_path) : asset('admin-assets/assets/img/avatars/1.png') }}"
                        class="d-block rounded" height="100" width="100" alt="company-logo">

                    <form method="POST" action="{{ route('admin.company.logo.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="button-wrapper">
                            <label class="btn btn-primary me-2 mb-2" tabindex="0">
                                <span class="d-none d-sm-block">Upload Logo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" name="logo" hidden accept="image/png, image/jpeg">
                            </label>

                            <button type="submit" class="btn btn-outline-secondary mb-2">
                                Save Logo
                            </button>

                            <p class="text-muted mb-0">JPG/PNG only. Max size 800KB</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <h5 class="card-header">Company Information</h5>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.company.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Company Name</label>
                            <input class="form-control" type="text" name="name" required
                                value="{{ old('name', $company->name) }}">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Legal Name</label>
                            <input class="form-control" type="text" name="legal_name"
                                value="{{ old('legal_name', $company->legal_name) }}">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Website</label>
                            <input class="form-control" type="text" name="website"
                                value="{{ old('website', $company->website) }}" placeholder="https://...">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email"
                                value="{{ old('email', $company->email) }}">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Phone</label>
                            <input class="form-control" type="text" name="phone"
                                value="{{ old('phone', $company->phone) }}">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Timezone</label>
                            <input class="form-control" type="text" name="timezone" required
                                value="{{ old('timezone', $company->timezone) }}">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Currency Code</label>
                            <input class="form-control" type="text" name="currency_code" required
                                value="{{ old('currency_code', $company->currency_code) }}" placeholder="USD">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Date Format</label>
                            <input class="form-control" type="text" name="date_format"
                                value="{{ old('date_format', $company->date_format) }}" placeholder="Y-m-d">
                        </div>

                        <div class="mb-3 col-md-12">
                            <label class="form-label">HQ Address</label>
                            <textarea class="form-control" name="hq_address" rows="2">{{ old('hq_address', $company->hq_address) }}</textarea>
                        </div>

                        <div class="mb-3 col-md-3">
                            <label class="form-label">City</label>
                            <input class="form-control" type="text" name="city"
                                value="{{ old('city', $company->city) }}">
                        </div>

                        <div class="mb-3 col-md-3">
                            <label class="form-label">State</label>
                            <input class="form-control" type="text" name="state"
                                value="{{ old('state', $company->state) }}">
                        </div>

                        <div class="mb-3 col-md-3">
                            <label class="form-label">Postal Code</label>
                            <input class="form-control" type="text" name="postal_code"
                                value="{{ old('postal_code', $company->postal_code) }}">
                        </div>

                        <div class="mb-3 col-md-3">
                            <label class="form-label">Country</label>
                            <input class="form-control" type="text" name="country"
                                value="{{ old('country', $company->country) }}">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Registration No</label>
                            <input class="form-control" type="text" name="registration_no"
                                value="{{ old('registration_no', $company->registration_no) }}">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Tax ID</label>
                            <input class="form-control" type="text" name="tax_id"
                                value="{{ old('tax_id', $company->tax_id) }}">
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
