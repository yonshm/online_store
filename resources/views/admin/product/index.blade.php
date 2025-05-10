    @extends('layouts.admin')
    @section('title', $viewData['title'])
    @section('content')
        <div class="card mb-4">
            <div class="card-header">
                Create Products
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
                                <label class="col-lg-2 col-md-6 col-sm-12 col-form-label">Name:</label>
                                <div class="col-lg-10 col-md-6 col-sm-12">
                                    <input name="name" value="{{ old('name') }}" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3 row">
                                <label class="col-lg-2 col-md-6 col-sm-12 col-form-label">Price:</label>
                                <div class="col-lg-10 col-md-6 col-sm-12">
                                    <input name="price" value="{{ old('price') }}" type="number" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3 row">
                                <label class="col-lg-2 col-md-6 col-sm-12 col-form-label">Image:</label>
                                <div class="col-lg-10 col-md-6 col-sm-12">
                                    <input class="form-control" type="file" name="image">
                                </div>
                            </div>

                        </div>
                        <div class="col">

                            <div class="mb-3 row">
                                <label class="col-lg-2 col-md-6 col-sm-12 col-form-label">Categorie:</label>
                                <div class="col-lg-10 col-md-6 col-sm-12">
                                    <select name="category_id" id="category_id" class="form-select" >
                                        <option value="-1">Select Category</option>
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
                            <label for="quantity_store">Quantite Store</label>
                            <input type="number" name="quantity_store" class="form-input">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    Manage Products
                </div>
                <div class="">
                    <select name="category_id_filtred" id="category_id_filtred" class="form-select">
                        <option value="-1">All Categories</option>
                        @foreach ($viewData['categories'] as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Description</th>
                            <th scope="col">Category</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Edit</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        @foreach ($viewData['products'] as $product)
                            <tr>
                                <td>{{ $product->getId() }}</td>
                                <td>{{ $product->getName() }}</td>
                                <td>{{ $product->getDescription() }}</td>
                                <td>{{ $product->getCategory() }}</td>
                                <td>{{ $product->getQuantity_store() }}</td>

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
                document.getElementById('category_id_filtred').addEventListener('change', function() {
                    categoryId = this.value;

                    fetch(`/admin/products/filter?category_id=${categoryId}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json()) 
                            .then(data => {
                            let rows = '';
                            
                            data.forEach(product => {
                                rows += `<tr>
                                    <td>${product.id}</td>
                                    <td>${product.name}</td>
                                    <td>${product.description}</td>
                                    <td>${product.category ? product.category.name : "No category"}</td>
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
                            if(data.length == 0){
                                document.getElementById('product-table-body').innerHTML = `
                                <tr class="text-center">
                                    <td colspan='6'>Aucune Product finded</td>    
                                </tr>`
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching product data:', error);
                        });
                });
            </script>
        @endpush
    @endsection
