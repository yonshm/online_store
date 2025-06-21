@extends('layouts.app')
@section('title', $viewData["title"])
@section('subtitle', $viewData["subtitle"])
@section('content')
<div class="mb-3">
  <form method="GET" action="{{ route('product.index') }}" class="d-flex align-items-center">
    <select name="filter" id="select">
      <option value="-1" {{ old('filter', request('filter')) == '-1' ? 'selected' : '' }}>{{ __('All Products') }}</option>
      <option value="discounted" {{ old('filter', request('filter')) == 'discounted' ? 'selected' : '' }}>{{ __('Discounted Products') }}</option>
    </select>
    <button type="submit" class="btn btn-sm btn-outline-primary ms-2">{{ __('Filter') }}</button>
  </form>
</div>
<div class="row">
  @foreach ($viewData["products"] as $product)
  <div class="col-md-4 col-lg-3 mb-2">
    <div class="card">
      <img src="https://fakeimg.pl/250x250/" class="card-img-top img-card">

      @if ($product->getQuantity_store() == 0)
        <div class="position-absolute top-0 start-0 m-2">
          <span class="badge bg-danger">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ __('Out of Stock') }}
          </span>
        </div>
      @endif
      <div class="card-body text-center">
        {{-- Affichage du prix barré et du prix remisé --}}
        @php
          $originalPrice = $product->getPrice();
          $discountedPrice = method_exists($product, 'getDiscountedPrice') ? $product->getDiscountedPrice() : $originalPrice;
        @endphp
        @if ($discountedPrice < $originalPrice)
          <span class="text-muted text-decoration-line-through">${{ $originalPrice }}</span>
          <span class="fw-bold ms-2 text-success">${{ $discountedPrice }}</span>
        @else
          <span class="fw-bold">${{ $originalPrice }}</span>
        @endif
        <br>
        <a href="{{ route('product.show', ['id'=> $product->getId()]) }}"
          class="btn bg-primary text-white mt-2">{{ $product->getName() }}</a>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection