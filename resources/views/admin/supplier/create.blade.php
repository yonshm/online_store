@extends('layouts.admin')
@section('title', 'Ajouter Fournisseurs')
@section('content')
<div class="container mt-4">
    <h1>{{ __('Add a supplier') }}</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('adminSuppliers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="raison_social" class="form-label">{{ __('Business Name') }} *</label>
            <input type="text" class="form-control" id="raison_social" name="raison_social" value="{{ old('raison_social') }}" required>
        </div>
        <div class="mb-3">
            <label for="adresse" class="form-label">{{ __('Address') }}</label>
            <input type="text" class="form-control" id="adresse" name="adresse" value="{{ old('adresse') }}">
        </div>
        <div class="mb-3">
            <label for="tele" class="form-label">{{ __('Phone') }}</label>
            <input type="text" class="form-control" id="tele" name="tele" value="{{ old('tele') }}">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">{{ __('Description') }}</label>
            <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">{{ __('Save') }}</button>
        <a href="{{ route('adminSuppliers.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
