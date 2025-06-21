@extends('layouts.app')
@section('title', $viewData['title'])
@section('subtitle', $viewData['subtitle'])
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Order Summary') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($viewData['products'] as $product)
                                        <tr>
                                            <td>{{ $product->getName() }}</td>
                                            <td>{{ $viewData['quantities'][$product->getId()] }}</td>
                                            <td>${{ $product->getPrice() }}</td>
                                            <td>${{ $product->getPrice() * $viewData['quantities'][$product->getId()] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <h5><strong>{{ __('Total: $') }}{{ $viewData['total'] }}</strong></h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>{{ __('Choose Payment Method') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-cash-coin text-success"></i>
                                        {{ __('Cash on Delivery') }}
                                    </h5>
                                    <p class="card-text">{{ __('Pay when you receive your order. No additional fees.') }}
                                    </p>
                                    <form action="{{ route('payment.cash-on-delivery') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary">
                                            {{ __('Choose Cash on Delivery') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-credit-card text-primary"></i>
                                        {{ __('Online Payment') }}
                                    </h5>
                                    <p class="card-text">{{ __('Pay securely online with your account balance.') }}</p>
                                    <form action="{{ route('payment.online') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success">
                                            {{ __('Pay Online') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>{{ __('Note:') }}</strong>
                            <ul class="mb-0 mt-2">
                                <li>{{ __('Cash on Delivery: Payment is due upon delivery') }}</li>
                                <li>{{ __('Online Payment: Payment is processed immediately from your account balance') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
