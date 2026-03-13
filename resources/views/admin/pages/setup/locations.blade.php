@extends('admin.layouts.layout')

@section('title', 'Admin - Locations')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Company Setup /</span> Locations
        </h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Locations</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLocation">
                    <i class="bx bx-plus me-1"></i> Add Location
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Used</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($locations as $i => $loc)
                            <tr>
                                <td>{{ $locations->firstItem() + $i }}</td>
                                <td><strong>{{ $loc->name }}</strong></td>
                                <td>{{ $loc->address ?? '-' }}</td>
                                <td><span class="badge bg-label-primary">{{ $loc->employments_count }}</span></td>

                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#editLocation{{ $loc->id }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </button>

                                            <form method="POST" action="{{ route('admin.locations.destroy', $loc->id) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Delete this location?')"
                                                    {{ $loc->employments_count > 0 ? 'disabled' : '' }}>
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Edit Modal --}}
                                    <div class="modal fade" id="editLocation{{ $loc->id }}" data-bs-backdrop="static"
                                        tabindex="-1">
                                        <div class="modal-dialog">
                                            <form class="modal-content" method="POST"
                                                action="{{ route('admin.locations.update', $loc->id) }}">
                                                @csrf @method('PUT')

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Location</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="mb-2">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control" required
                                                            value="{{ $loc->name }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Address</label>
                                                        <textarea name="address" class="form-control" rows="2">{{ $loc->address }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    {{-- /Edit Modal --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No locations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $locations->links() }}</div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addLocation" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.locations.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                new bootstrap.Modal(document.getElementById('addLocation')).show();
            });
        </script>
    @endif

@endsection
