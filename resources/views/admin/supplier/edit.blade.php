@extends('layouts.app')

@section('title', 'Modifier supplier')

@section('content')
<div class="container">
    <h2 class="mb-4">Modifier le supplier</h2>

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
            <label for="raison_sociale" class="form-label">Raison sociale</label>
            <input type="text" class="form-control" id="raison_sociale" name="raison_social" value="{{ old('raison_sociale', $supplier->raison_social) }}" required>
        </div>

        <div class="mb-3">
            <label for="adresse" class="form-label">Adresse</label>
            <input type="text" class="form-control" id="adresse" name="adresse" value="{{ old('adresse', $supplier->adresse) }}" required>
        </div>

        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="text" class="form-control" id="telephone" name="tele" value="{{ old('tele', $supplier->tele) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $supplier->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $supplier->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
        <a href="{{ route('adminSuppliers.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>
@endsection
