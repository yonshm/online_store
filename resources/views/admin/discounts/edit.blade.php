@extends('layouts.app')
@section('title', 'Edit Discount')
@section('subtitle', 'Modify the discount details')
@section('content')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3>{{ __('Edit Discount') }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('discounts.update', $discount->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="discount_percentage">{{ __('Discount Percentage') }} (%)</label>
                            <input type="number" name="discount_percentage" id="discount_percentage" class="form-control" value="{{ old('discount_percentage', $discount->discount_percentage) }}" required min="0" max="100">
                            
                        </div>

                        <div class="form-group">
                            <label for="start_date">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" id="start_date" class="form-control " value="{{ old('start_date', $discount->start_date) }}" required>
                            
                        </div>

                        <div class="form-group">
                            <label for="end_date">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $discount->end_date) }}" required>
                            </div>

                        <div class="form-group">
                            <label for="type">{{ __('Discount Type') }}</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="global" {{ $discount->type === 'all' ? 'selected' : '' }}>{{ __('For all products') }}</option>
                                <option value="product" {{ $discount->type === 'product' ? 'selected' : '' }}>{{ __('For specific product') }}</option>
                                <option value="category" {{ $discount->type === 'category' ? 'selected' : '' }}>{{ __('For specific category') }}</option>
                            </select>
                        </div>

                        <div class="form-group" id="product_field" style="display: {{ $discount->type == 'product' ? 'block' : 'none' }};">
                            <label for="product_id">{{ __('Select Product') }}</label>
                            <select name="product_id" id="product_id" class="form-control">
                                <option value="">{{ __('Choose a product') }}</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ $discount->product_id == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="category_field" style="display: {{ $discount->type == 'category' ? 'block' : 'none' }};">
                            <label for="category_id">{{ __('Select Category') }}</label>
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="">{{ __('Choose a category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $discount->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                        <a href="{{ route('discounts.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    @endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('type').addEventListener('change', function() {
            let type = this.value;
            console.log(type)
            document.getElementById('product_field').style.display = type === 'product' ? 'block' : 'none';
            document.getElementById('category_field').style.display = type === 'category' ? 'block' : 'none';
        })
        });
    </script>
@endpush
