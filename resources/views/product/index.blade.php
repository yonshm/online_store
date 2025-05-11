@extends('layouts.app')
@section('title', $viewData["title"])
@section('subtitle', $viewData["subtitle"])
@section('content')
<div class="row">
  @foreach ($viewData["products"] as $product)
  <div class="col-md-4 col-lg-3 mb-2">
    <div class="card">
      <img src="https://fakeimg.pl/250x250/" class="card-img-top img-card">

       @if ($product->getQuantity_store() == 0)
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-danger">
                                <i class="bi bi-exclamation-triangle-fill"></i> Out of Stock
                            </span>
                        </div>
                    @endif
      <div class="card-body text-center">
        <a href="{{ route('product.show', ['id'=> $product->getId()]) }}"
          class="btn bg-primary text-white">{{ $product->getName() }}</a>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endsection
