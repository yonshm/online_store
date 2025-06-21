@extends('layouts.admin')
@section('title', $viewData['title'])
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>{{ __('Order Details') }} #{{ $viewData['order']->getId() }}</h4>
            <a href="{{ route('admin.order.index') }}" class="btn btn-secondary">
                <i class="bi-arrow-left"></i> {{ __('Back to Orders') }}
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <h5>{{ __('Customer Information') }}</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>{{ __('Name:') }}</strong></td>
                            <td>{{ $viewData['order']->user ? $viewData['order']->user->getName() : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('Email:') }}</strong></td>
                            <td>{{ $viewData['order']->user ? $viewData['order']->user->getEmail() : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('Balance:') }}</strong></td>
                            <td>${{ $viewData['order']->user ? $viewData['order']->user->getBalance() : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>{{ __('Order Information') }}</h5>
                    <table class="table table-borderless">
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
                            <td>
                                @if ($viewData['order']->getPaymentMethod() == 'cash_on_delivery')
                                    <span class="badge bg-primary">{{ __('Cash on Delivery') }}</span>
                                @else
                                    <span class="badge bg-success">{{ __('Online Payment') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('Payment Status:') }}</strong></td>
                            <td>
                                <form
                                    action="{{ route('admin.order.update-payment-status', $viewData['order']->getId()) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="payment_status" class="form-select form-select-sm d-inline-block w-auto"
                                        onchange="this.form.submit()">
                                        <option value="pending"
                                            {{ $viewData['order']->getPaymentStatus() == 'pending' ? 'selected' : '' }}>
                                            {{ __('Pending') }}</option>
                                        <option value="paid"
                                            {{ $viewData['order']->getPaymentStatus() == 'paid' ? 'selected' : '' }}>
                                            {{ __('Paid') }}</option>
                                        <option value="failed"
                                            {{ $viewData['order']->getPaymentStatus() == 'failed' ? 'selected' : '' }}>
                                            {{ __('Failed') }}</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            <h5>{{ __('Order Items') }}</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Item ID') }}</th>
                            <th>{{ __('Product Name') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($viewData['order']->getItems() as $item)
                            <tr>
                                <td>{{ $item->getId() }}</td>
                                <td>
                                    @if ($item->getProduct())
                                        <a href="{{ route('product.show', ['id' => $item->getProduct()->getId()]) }}"
                                            target="_blank">
                                            {{ $item->getProduct()->getName() }}
                                        </a>
                                    @else
                                        {{ __('Product not found') }}
                                    @endif
                                </td>
                                <td>${{ $item->getPrice() }}</td>
                                <td>{{ $item->getQuantity() }}</td>
                                <td>${{ $item->getPrice() * $item->getQuantity() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>{{ __('Total:') }}</strong></td>
                            <td><strong>${{ $viewData['order']->getTotal() }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
