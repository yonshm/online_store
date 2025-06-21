@extends('layouts.admin')
@section('title', 'Modifier Fournisseurs')
@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('Edit Supplier') }}</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

<form action="{{ route('adminSuppliers.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="raison_sociale" class="form-label">{{ __('Business Name') }}</label>
            <input type="text" class="form-control" id="raison_sociale" name="raison_social" value="{{ old('raison_sociale', $supplier->raison_social) }}" required>
        </div>

        <div class="mb-3">
            <label for="adresse" class="form-label">{{ __('Address') }}</label>
            <input type="text" class="form-control" id="adresse" name="adresse" value="{{ old('adresse', $supplier->adresse) }}" required>
        </div>

        <div class="mb-3">
            <label for="telephone" class="form-label">{{ __('Phone') }}</label>
            <input type="text" class="form-control" id="telephone" name="tele" value="{{ old('tele', $supplier->tele) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $supplier->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">{{ __('Description') }}</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $supplier->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">{{ __('Save Changes') }}</button>
        <a href="{{ route('adminSuppliers.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
