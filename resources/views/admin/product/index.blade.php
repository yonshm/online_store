    @extends('layouts.admin')
    @section('title', $viewData['title'])
    @section('content')
        <div class="card mb-4">
            <div class="card-header">
                {{ __('Create Products') }}
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <ul class="alert alert-danger list-unstyled">
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('admin.product.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col">
                            <div class="mb-3 row">
                                <label class="col-lg-2 col-md-6 col-sm-12 col-form-label">{{ __('Name') }}:</label>
                                <div class="col-lg-10 col-md-6 col-sm-12">
                                    <input name="name" value="{{ old('name') }}" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3 row">
                                <label class="col-lg-2 col-md-6 col-sm-12 col-form-label">{{ __('Price') }}:</label>
                                <div class="col-lg-10 col-md-6 col-sm-12">
                                    <input name="price" value="{{ old('price') }}" type="number" class="form-control">
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
                                    <select name="category_id" id="category_id" class="form-select">
                                        <option value="-1">{{ __('Select Category') }}</option>
                                        @foreach ($viewData['categories'] as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col">
                            <label for="quantity_store">{{ __('Store Quantity') }}</label>
                            <input type="number" name="quantity_store" class="form-input">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">{{ __('Supplier') }}</label>
                        <select name="supplier_id" id="supplier_id" class="form-control">
                            <option value="">-- {{ __('Choose a supplier') }} --</option>
                            @foreach ($viewData['suppliers'] as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->raison_social }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    {{ __('Manage Products') }}
                </div>
                <div class="">
                    <select name="category_id_filtred" id="category_id_filtred" class="form-select">
                        <option value="-1">{{ __('All Categories') }}</option>
                        @foreach ($viewData['categories'] as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="">
                    <select name="supplier_id_filtered" id="supplier_id_filtered" class="form-select">
                        <option value="-1">{{ __('All Suppliers') }}</option>
                        @foreach ($viewData['suppliers'] as $supplier)
                            <option value="{{ $supplier->id}}">{{$supplier->raison_social}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('ID') }}</th>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Description') }}</th>
                            <th scope="col">{{ __('Category') }}</th>
                            <th scope="col">{{ __('Stock') }}</th>
                            <th scope="col">{{ __('Supplier') }}</th>
                            <th scope="col">{{ __('Edit') }}</th>
                            <th scope="col">{{ __('Delete') }}</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        @foreach ($viewData['products'] as $product)
                            @php
                                $bgColor = '';
                                if ($product->quantity_store == 0) {
                                    $bgColor = 'table-danger';
                                } elseif ($product->quantity_store < 10) {
                                    $bgColor = 'table-warning';
                                } else {
                                    $bgColor = 'table-success';
                                }
                            @endphp
                            <tr class="{{ $bgColor }}">
                                <td>{{ $product->getId() }}</td>
                                <td>{{ $product->getName() }}</td>
                                <td>{{ $product->getDescription() }}</td>
                                <td>{{ $product->getCategory() }}</td>
                                <td>{{ $product->getQuantity_store() }}</td>

                                <td>{{ $product->supplier ? $product->supplier->raison_social : 'N/A' }}</td>
                                <td>
                                    <a class="btn btn-primary"
                                        href="{{ route('admin.product.edit', ['id' => $product->getId()]) }}">
                                        <i class="bi-pencil"></i>
                                    </a>
                                </td>
                                <td>
                                    <form action="{{ route('admin.product.delete', $product->getId()) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-danger">
                                            <i class="bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @push('scripts')
            <script>
                document.getElementById('category_id_filtred').addEventListener('change', fetchFilteredProducts);
                document.getElementById('supplier_id_filtered').addEventListener('change', fetchFilteredProducts);

                function fetchFilteredProducts() {
                    const categoryId = document.getElementById('category_id_filtred').value;
                    const supplierId = document.getElementById('supplier_id_filtered').value;

                    fetch(`/admin/products/filter?category_id=${categoryId}&&supplier_id=${supplierId}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            let bgColor = '';

                            let rows = '';

                            data.forEach(product => {
                                if (product.quantity_store == 0) {
                                    bgColor = 'table-danger';
                                } else if (product.quantity_store < 10) {
                                    bgColor = 'table-warning';
                                } else {
                                    bgColor = 'table-success';
                                }
                                rows += `<tr class="${bgColor}">
                                    <td>${product.id}</td>
                                    <td>${product.name}</td>
                                    <td>${product.description}</td>
                                    <td>${product.category ? product.category.name : "No category"}</td>
                                    <td>${product.quantity_store}</td>
                                    <td>${product.supplier ? product.supplier.raison_social : 'N/A'}</td>
                                    <td>
                                        <a class="btn btn-primary" href="/admin/product/edit/${product.id}">
                                            <i class="bi-pencil"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <form action="/admin/product/delete/${product.id}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger">
                                                <i class="bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>`;
                            });

                            document.getElementById('product-table-body').innerHTML = rows;
                            if (data.length == 0) {
                                document.getElementById('product-table-body').innerHTML = `
                                <tr class="text-center">
                                    <td colspan='8'>Aucune Product finded</td>    
                                </tr>`
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching product data:', error);
                        });
                };
            </script>
        @endpush
    @endsection
