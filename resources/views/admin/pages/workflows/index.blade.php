@extends('admin.layouts.layout')

@section('title', 'Admin - Workflows')

@section('main-content')


    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Workflow Builder</h4>
            <a href="{{ route('admin.workflows.create') }}" class="btn btn-primary">Create Workflow</a>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Module</th>
                            <th>Steps</th>
                            <th>Status</th>
                            <th>Conditions</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workflows as $workflow)
                            <tr>
                                <td>{{ $workflow->name }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $workflow->module)) }}</td>
                                <td>{{ $workflow->steps->count() }}</td>
                                <td>
                                    <span
                                        class="badge {{ $workflow->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">
                                        {{ $workflow->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $workflow->conditions->count() }}</td>
                                <td>
                                    <a href="{{ route('admin.workflows.edit', $workflow) }}"
                                        class="btn btn-sm btn-outline-primary">Edit</a>

                                    <form action="{{ route('admin.workflows.destroy', $workflow) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Delete this workflow?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No workflows found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body">
                {{ $workflows->links() }}
            </div>
        </div>
    </div>
@endsection
