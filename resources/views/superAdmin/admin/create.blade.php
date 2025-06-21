@extends('layouts.admin')
@section('title', 'Create Admin')
@section('content')
<div class="container">
    <h2>{{ __('Create New Admin') }}</h2>
    <form action="{{ route('superAdmin.users.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email address') }}</label>
            <input type="email" name="email" class="form-control" id="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input type="password" name="password" class="form-control" id="password" required>
        </div>
        <button type="submit" class="btn btn-primary">{{ __('Create Admin') }}</button>
    </form>
</div>
@endsection