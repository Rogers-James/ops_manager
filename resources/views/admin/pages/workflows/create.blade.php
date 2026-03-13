@extends('admin.layouts.layout')

@section('title', 'Admin - Workflow Create')

@section('main-content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Create Workflow</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.workflows.store') }}" method="POST">
                    @include('admin.pages.workflows._form')
                </form>
            </div>
        </div>
    </div>
    
@endsection
