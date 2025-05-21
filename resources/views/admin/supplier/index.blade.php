@extends('layouts.app')
@section('title', 'Liste des fournisseurs')
@section('content')
    <div class="container mt-4">
        <h1>Fournisseurs</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('adminSuppliers.create') }}" class="btn btn-primary mb-3">Ajouter un fournisseur</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Raison sociale</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->id }}</td>
                        <td>{{ $supplier->raison_social }}</td>
                        <td>{{ $supplier->adresse }}</td>
                        <td>{{ $supplier->tele }}</td>
                        <td>{{ $supplier->email }}</td>
                        <td>{{ $supplier->description }}</td>
                        <td>
                            <a href="{{ route('adminSuppliers.edit', $supplier->id) }}"
                                class="btn btn-sm btn-warning">Modifier</a>

                            <form action="{{ route('adminSuppliers.destroy', $supplier->id) }}" method="POST"
                                style="display:inline-block" onsubmit="return confirm('Voulez-vous vraiment supprimer ce fournisseur ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
