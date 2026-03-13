@extends('admin.layouts.layout')

@section('title', 'Admin - Grades')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Company Setup /</span> Grades
        </h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Grades</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addGrade">
                    <i class="bx bx-plus me-1"></i> Add Grade
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Rank</th>
                            <th>Used</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody class="table-border-bottom-0">
                        @forelse($grades as $i => $g)
                            <tr>
                                <td>{{ $grades->firstItem() + $i }}</td>
                                <td><strong>{{ $g->name }}</strong></td>
                                <td>{{ $g->rank ?? '-' }}</td>
                                <td><span class="badge bg-label-primary">{{ $g->employments_count }}</span></td>

                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>

                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#editGrade{{ $g->id }}">
                                                <i class="bx bx-edit-alt me-1"></i> Edit
                                            </button>

                                            <form method="POST" action="{{ route('admin.grades.destroy', $g->id) }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Delete this grade?')"
                                                    {{ $g->employments_count > 0 ? 'disabled' : '' }}>
                                                    <i class="bx bx-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Edit Modal --}}
                                    <div class="modal fade" id="editGrade{{ $g->id }}" data-bs-backdrop="static"
                                        tabindex="-1">
                                        <div class="modal-dialog">
                                            <form class="modal-content" method="POST"
                                                action="{{ route('admin.grades.update', $g->id) }}">
                                                @csrf @method('PUT')

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Grade</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="mb-2">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" name="name" class="form-control" required
                                                            value="{{ $g->name }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Rank</label>
                                                        <input type="number" name="rank" class="form-control"
                                                            value="{{ $g->rank }}">
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
                                <td colspan="5" class="text-center py-4">No grades found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer">{{ $grades->links() }}</div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addGrade" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.grades.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Grade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Rank</label>
                        <input type="number" name="rank" class="form-control" value="{{ old('rank') }}">
                        <small class="text-muted">Optional. Used for sorting grades.</small>
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
                new bootstrap.Modal(document.getElementById('addGrade')).show();
            });
        </script>
    @endif

@endsection
