@extends('layouts.admin')
@section('title', 'Fournisseurs')
@section('content')
    <div class="container mt-4">
        <h1>{{ __('Suppliers') }}</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('adminSuppliers.create') }}" class="btn btn-primary mb-3">{{ __('Add a supplier') }}</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Business Name') }}</th>
                    <th>{{ __('Address') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Actions') }}</th>
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
                                class="btn btn-sm btn-warning">{{ __('Edit') }}</a>

                            <form action="{{ route('adminSuppliers.destroy', $supplier->id) }}" method="POST"
                                style="display:inline-block" onsubmit="return confirm('{{ __('Do you really want to delete this supplier?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
