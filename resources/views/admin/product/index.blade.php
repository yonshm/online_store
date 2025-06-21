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
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.product.export.csv') }}" class="btn btn-success">
                        <i class="bi-download"></i> {{ __('Export CSV') }}
                    </a>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi-upload"></i> {{ __('Import CSV') }}
                    </button>
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
                            <option value="{{ $supplier->id }}">{{ $supplier->raison_social }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="card-body">
                @if (session('import_message'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('import_message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
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

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $viewData['products']->links() }}
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.getElementById('category_id_filtred').addEventListener('change', fetchFilteredProducts);
                document.getElementById('supplier_id_filtered').addEventListener('change', fetchFilteredProducts);

                function fetchFilteredProducts(page = 1) {
                    const categoryId = document.getElementById('category_id_filtred').value;
                    const supplierId = document.getElementById('supplier_id_filtered').value;

                    fetch(`/admin/products/filter?category_id=${categoryId}&&supplier_id=${supplierId}&page=${page}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            let bgColor = '';
                            let rows = '';

                            data.data.forEach(product => {
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

                            // Update pagination
                            updatePagination(data);

                            if (data.data.length == 0) {
                                document.getElementById('product-table-body').innerHTML = `
                                <tr class="text-center">
                                    <td colspan='8'>Aucune Product finded</td>    
                                </tr>`
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching product data:', error);
                        });
                }

                function updatePagination(data) {
                    const paginationContainer = document.querySelector('.pagination-container');
                    if (!paginationContainer) {
                        const paginationDiv = document.createElement('div');
                        paginationDiv.className = 'd-flex justify-content-center mt-4 pagination-container';
                        document.querySelector('.card-body').appendChild(paginationDiv);
                    }

                    let paginationHtml = '<ul class="pagination">';

                    // Previous page
                    if (data.prev_page_url) {
                        paginationHtml +=
                            `<li class="page-item"><a class="page-link" href="#" onclick="fetchFilteredProducts(${data.current_page - 1})">Previous</a></li>`;
                    }

                    // Page numbers
                    for (let i = 1; i <= data.last_page; i++) {
                        const activeClass = i === data.current_page ? 'active' : '';
                        paginationHtml +=
                            `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="fetchFilteredProducts(${i})">${i}</a></li>`;
                    }

                    // Next page
                    if (data.next_page_url) {
                        paginationHtml +=
                            `<li class="page-item"><a class="page-link" href="#" onclick="fetchFilteredProducts(${data.current_page + 1})">Next</a></li>`;
                    }

                    paginationHtml += '</ul>';

                    const container = document.querySelector('.pagination-container');
                    if (container) {
                        container.innerHTML = paginationHtml;
                    }
                }
            </script>
        @endpush

        <!-- Import CSV Modal -->
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">{{ __('Import Products from CSV') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.product.import.csv') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="csv_file" class="form-label">{{ __('Select CSV File') }}</label>
                                <input type="file" class="form-control" id="csv_file" name="csv_file"
                                    accept=".csv" required>
                                <div class="form-text">
                                    {{ __('The CSV file should have the following columns: ID, Name, Description, Price, Image, Category ID, Category Name, Quantity Store, Supplier ID, Supplier Name, Created At, Updated At') }}
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <strong>{{ __('Note:') }}</strong>
                                {{ __('Products with the same name will be updated. New products will be created.') }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Import') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection
