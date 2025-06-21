@extends('layouts.app')
@section('title', $viewData["title"])
@section('subtitle', $viewData["subtitle"])
@section('content')
<div class="card mb-3">
  @if($errors->any())
    <ul class="alert alert-danger list-unstyled">
      @foreach($errors->all() as $error)
      <li>- {{ $error }}</li>
      @endforeach
    </ul>
  @endif

  @if (session('error'))
    <ul class="alert alert-danger list-unstyled">
      <li>- {{ session('error') }}</li>
    </ul>
  @endif
  <div class="row g-0">
    <div class="col-md-4">
      <img src="{{ asset('/storage/'.$viewData["product"]->getImage()) }}" class="img-fluid rounded-start">
    </div>
     @php
          $originalPrice = $viewData["product"]->getPrice();
          $discountedPrice = method_exists($viewData["product"], 'getDiscountedPrice') ? $viewData["product"]->getDiscountedPrice() : $originalPrice;
      @endphp
      
    <div class="col-md-8">
      <div class="card-body">
        @if ($discountedPrice < $originalPrice)
          <span class="text-muted text-decoration-line-through">${{ $originalPrice }}</span>
          <span class="fw-bold ms-2 text-success">${{ $discountedPrice }}</span>
        @else
          <span class="fw-bold">${{ $originalPrice }}</span>
        @endif

        <p class="card-text">{{ $viewData["product"]->getDescription() }}</p>
        <p class="card-text">
        <form method="POST" action="{{ route('cart.add', ['id'=> $viewData['product']->getId()]) }}">
          <div class="row">
            @csrf
            <div class="col-auto">
              <div class="input-group col-auto">
                <div class="input-group-text">{{ __('Quantity') }}</div>
                <input type="number" min="1" max="50" class="form-control quantity-input" name="quantity" value="1">
              </div>
            </div>
            <div class="col-auto">
              <button class="btn bg-primary text-white" type="submit">{{ __('Add to Cart') }}</button>
            </div>
          </div>
        </form>
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
