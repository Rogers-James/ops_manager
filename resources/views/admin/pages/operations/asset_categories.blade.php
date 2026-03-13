@extends('admin.layouts.layout')

@section('title', 'Admin - Asset Categories')

@section('main-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-light">Operations / Assets /</span> Asset Categories</h4>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategory">
                <i class="bx bx-plus me-1"></i> Add Category
            </button>
        </div>

        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $i => $c)
                            <tr>
                                <td>{{ $categories->firstItem() + $i }}</td>
                                <td><strong>{{ $c->name }}</strong></td>
                                <td>{{ $c->code ?? '-' }}</td>
                                <td class="d-flex gap-1">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editCategory-{{ $c->id }}">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.asset_categories.destroy', $c) }}"
                                        onsubmit="return confirm('Delete this category?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $categories->links() }}</div>
        </div>
    </div>


    @foreach ($categories as $c)
        <div class="modal fade" id="editCategory-{{ $c->id }}" data-bs-backdrop="static" tabindex="-1"
            aria-hidden="true">
            <div class="modal-dialog">
                <form class="modal-content" method="POST" action="{{ route('admin.asset_categories.update', $c) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Name</label>
                            <input class="form-control" name="name" value="{{ $c->name }}" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Code</label>
                            <input class="form-control" name="code" value="{{ $c->code }}">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
    <div class="modal fade" id="addCategory" data-bs-backdrop="static" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="{{ route('admin.asset_categories.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Name</label>
                        <input class="form-control" name="name" required>
                    </div>
                    {{-- <div class="mb-2">
                        <label class="form-label">Code</label>
                        <input class="form-control" name="code">
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
