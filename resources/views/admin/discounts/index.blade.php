


@extends('layouts.admin')
@section('title', 'List and Add Discounts')
@section('content')
<div class="row">
    <!-- Add Discount Form -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h5>Add New Discount</h5></div>
            <div class="card-body">
                <form action="{{ route('discounts.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="discount_percentage">Discount Percentage (%)</label>
                        <input type="number" name="discount_percentage" class="form-control" required min="0" max="100">
                    </div>

                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="type">Discount Type</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="">Choose type</option>
                            <option value="global">For all products</option>
                            <option value="product">For specific product</option>
                            <option value="category">For specific category</option>
                        </select>
                    </div>

                    <div class="form-group" id="product_field" style="display: none;">
                        <label for="product_id">Select Product</label>
                        <select name="product_id" class="form-control">
                            <option value="">Choose a product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="category_field" style="display: none;">
                        <label for="category_id">Select Category</label>
                        <select name="category_id" class="form-control">
                            <option value="">Choose a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success mt-2">Create Discount</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Discount List Table -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><h5>Existing Discounts</h5></div>
            <div class="card-body">
                @if ($discounts->count())
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Percentage</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($discounts as $discount)
                                <tr>
                                    <td>{{ $discount->discount_percentage }}%</td>
                                    <td>{{ $discount->start_date }}</td>
                                    <td>{{ $discount->end_date }}</td>
                                    <td>
                                        @if ($discount->type === 'global')
                                            Global
                                        @elseif ($discount->type === 'product' && $discount->product)
                                            Product : {{ $discount->product->name }}
                                        @elseif ($discount->type === 'category' && $discount->category)
                                            Category : {{ $discount->category->name }}
                                        @else
                                            {{ ucfirst($discount->type) }}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('discounts.edit', $discount->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Del</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No discounts available.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('type').addEventListener('change', function(){
        console.log(this.value);
    });
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const productField = document.getElementById('product_field');
        const categoryField = document.getElementById('category_field');

        function toggleFields() {
            const value = typeSelect.value;
            productField.style.display = value === 'product' ? 'block' : 'none';
            categoryField.style.display = value === 'category' ? 'block' : 'none';
        }
        
        typeSelect.addEventListener('change', toggleFields);
    });
</script>
@endpush
@endsection
