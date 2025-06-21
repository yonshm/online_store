@extends('layouts.admin')
@section('title', $viewData['title'])
@section('content')
    <div class="card mb-4">
        <div class="card-header">
            {{ __('Edit Product') }}
        </div>
        <div class="card-body">
            @if ($errors->any())
                <ul class="alert alert-danger list-unstyled">
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('admin.product.update', ['id' => $viewData['product']->getId()]) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col">
                        <div class="mb-3 row">
                            <label class="col-lg-2 col-md-6 col-sm-12 col-form-label">{{ __('Name') }}:</label>
                            <div class="col-lg-10 col-md-6 col-sm-12">
                                <input name="name" value="{{ $viewData['product']->getName() }}" type="text"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3 row">
                            <label class="col-lg-2 col-md-6 col-sm-12 col-form-label">{{ __('Price') }}:</label>
                            <div class="col-lg-10 col-md-6 col-sm-12">
                                <input name="price" value="{{ $viewData['product']->getPrice() }}" type="number"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="mb-3 row">
                            <label class="col-lg-2 col-md-6 col-sm-12 col-form-label">{{ __('Image') }}:</label>
                            <div class="col-lg-10 col-md-6 col-sm-12">
                                <input class="form-control" type="file" name="image">
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3 row">
                            <label class="col-lg-2 col-md-6 col-sm-12 col-form-label">{{ __('Category') }}:</label>
                            <div class="col-lg-10 col-md-6 col-sm-12">
                                <select name="category_id" id="category_id">
                                    <option value="-1">{{ __('Select Category') }}</option>
                                    @foreach ($viewData['categories'] as $cat)
                                        <option value="{{ $cat->id }}"
                                            {{ $cat->name == $viewData['product']->getCategory() ? 'selected' : '' }}>
                                            {{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">{{ __('Supplier') }}</label>
                        <select name="supplier_id" id="supplier_id" class="form-control">
                            <option value="">-- {{ __('Choose a supplier') }} --</option>
                            @foreach ($viewData['suppliers'] as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ $supplier->id == $viewData['product']->supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->raison_social }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col">
                        <label for="quantity_store">{{ __('Store Quantity') }}</label>
                        <input type="number" name="quantity_store" class="form-input"
                            value="{{ $viewData['product']->getQuantity_store() }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Description') }}</label>
                    <textarea class="form-control" name="description" rows="3">{{ $viewData['product']->getDescription() }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Edit') }}</button>
            </form>
        </div>
    </div>
@endsection
