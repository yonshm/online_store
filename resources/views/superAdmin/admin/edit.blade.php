@extends('layouts.admin')
@section('title', 'Create Admin')
@section('content')
<div class="container">
    <h2>{{ __('Edit Admin') }}</h2>
    <form action="{{ route('superAdmin.users.update', $admin->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $admin->name) }}" required>
        </div>
        
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email address') }}</label>
            <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $admin->email) }}" required>
        </div>
        
        <div class="mb-3">
            <label for="role" class="form-label">{{ __('Role') }}</label>
            <select name="role" id="role" class="form-select" required>
                <option value="admin" {{ old('role', $admin->role) == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                <option value="superadmin" {{ old('role', $admin->role) == 'superadmin' ? 'selected' : '' }}>{{ __('Super Admin') }}</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">{{ __('Update Admin') }}</button>
        <a href="{{ route('superAdmin.users.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection