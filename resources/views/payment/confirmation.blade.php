@extends('layouts.app')
@section('title', $viewData['title'])
@section('subtitle', $viewData['subtitle'])
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>
                            @if ($viewData['paymentMethod'] == 'Cash on Delivery')
                                <i class="bi bi-cash-coin text-success"></i>
                                {{ __('Order Confirmed - Cash on Delivery') }}
                            @else
                                <i class="bi bi-check-circle text-success"></i>
                                {{ __('Payment Successful - Online Payment') }}
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        @if ($viewData['paymentMethod'] == 'Cash on Delivery')
                            <div class="alert alert-warning" role="alert">
                                <h5 class="alert-heading">
                                    <i class="bi bi-info-circle"></i>
                                    {{ __('Cash on Delivery Order') }}
                                </h5>
                                <p>{{ __('Your order has been confirmed and will be delivered to your address.') }}</p>
                                <p><strong>{{ __('Payment Method:') }}</strong> {{ __('Cash on Delivery') }}</p>
                                <p><strong>{{ __('Payment Status:') }}</strong> <span
                                        class="badge bg-warning">{{ __('Pending - Pay upon delivery') }}</span></p>
                            </div>
                        @else
                            <div class="alert alert-success" role="alert">
                                <h5 class="alert-heading">
                                    <i class="bi bi-check-circle"></i>
                                    {{ __('Payment Completed Successfully') }}
                                </h5>
                                <p>{{ __('Your payment has been processed and your order is confirmed.') }}</p>
                                <p><strong>{{ __('Payment Method:') }}</strong> {{ __('Online Payment') }}</p>
                                <p><strong>{{ __('Payment Status:') }}</strong> <span
                                        class="badge bg-success">{{ __('Paid') }}</span></p>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <h5>{{ __('Order Details') }}</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>{{ __('Order Number:') }}</strong></td>
                                        <td>#{{ $viewData['order']->getId() }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Order Date:') }}</strong></td>
                                        <td>{{ $viewData['order']->getCreatedAt() }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Total Amount:') }}</strong></td>
                                        <td><strong>${{ $viewData['order']->getTotal() }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Payment Method:') }}</strong></td>
                                        <td>{{ $viewData['paymentMethod'] }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5>{{ __('Next Steps') }}</h5>
                                @if ($viewData['paymentMethod'] == 'Cash on Delivery')
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="bi bi-clock text-warning"></i>
                                            {{ __('Your order is being processed') }}
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-truck text-info"></i>
                                            {{ __('You will receive a delivery notification') }}
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-cash text-success"></i>
                                            {{ __('Prepare cash payment for delivery') }}
                                        </li>
                                    </ul>
                                @else
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="bi bi-clock text-warning"></i>
                                            {{ __('Your order is being processed') }}
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-truck text-info"></i>
                                            {{ __('You will receive a delivery notification') }}
                                        </li>
                                        <li class="list-group-item">
                                            <i class="bi bi-check-circle text-success"></i>
                                            {{ __('Payment completed - no further action needed') }}
                                        </li>
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('home.index') }}" class="btn btn-primary">
                                <i class="bi bi-house"></i>
                                {{ __('Continue Shopping') }}
                            </a>
                            <a href="{{ route('myaccount.orders') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-list-ul"></i>
                                {{ __('View My Orders') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
