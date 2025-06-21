@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('content')
    <div class="container">
        <div class="card mt-4">
            <div class="card-header">
                <h2>{{ __('All Admins') }}</h2>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Created At') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($admins as $admin)
                            <tr>
                                <td>{{ $admin->name }}</td>
                                <td>{{ $admin->email }}</td>
                                <td>{{ $admin->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('superAdmin.users.edit', $admin->id) }}"
                                        class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                                    <form action="{{ route('superAdmin.users.delete', $admin->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $admins->links() }}
                </div>

                <a href="{{ route('superAdmin.users.create') }}"
                    class="btn btn-success mt-3">{{ __('Add New Admin') }}</a>
            </div>
        </div>
    </div>
@endsection
