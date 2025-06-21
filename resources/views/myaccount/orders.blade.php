@extends('layouts.app')
@section('title', $viewData['title'])
@section('subtitle', $viewData['subtitle'])
@section('content')
    @forelse ($viewData["orders"] as $order)
        <div class="card mb-4">
            <div class="card-header">
                Order #{{ $order->getId() }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <b>Date:</b> {{ $order->getCreatedAt() }}<br />
                        <b>Total:</b> ${{ $order->getTotal() }}<br />
                        <b>Payment Method:</b>
                        @if ($order->getPaymentMethod() == 'cash_on_delivery')
                            <span class="badge bg-primary">{{ __('Cash on Delivery') }}</span>
                        @else
                            <span class="badge bg-success">{{ __('Online Payment') }}</span>
                        @endif
                        <br />
                        <b>Payment Status:</b>
                        @if ($order->getPaymentStatus() == 'pending')
                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                        @elseif($order->getPaymentStatus() == 'paid')
                            <span class="badge bg-success">{{ __('Paid') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('Failed') }}</span>
                        @endif
                    </div>
                </div>
                <table class="table table-bordered table-striped text-center mt-3">
                    <thead>
                        <tr>
                            <th scope="col">Item ID</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->getItems() as $item)
                            <tr>
                                <td>{{ $item->getId() }}</td>
                                <td>
                                    <a class="link-success"
                                        href="{{ route('product.show', ['id' => $item->getProduct()->getId()]) }}">
                                        {{ $item->getProduct()->getName() }}
                                    </a>
                                </td>
                                <td>${{ $item->getPrice() }}</td>
                                <td>{{ $item->getQuantity() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="alert alert-danger" role="alert">
            Seems to be that you have not purchased anything in our store =(.
        </div>
    @endforelse
@endsection
