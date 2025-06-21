@extends('layouts.admin')
@section('title', $viewData['title'])
@section('content')
    <div class="card">
        <div class="card-header">
            <h4>{{ __('Manage Orders') }}</h4>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Order ID') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Payment Method') }}</th>
                            <th>{{ __('Payment Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($viewData['orders'] as $order)
                            <tr>
                                <td>#{{ $order->getId() }}</td>
                                <td>{{ $order->user ? $order->user->getName() : 'N/A' }}</td>
                                <td>{{ $order->getCreatedAt() }}</td>
                                <td>${{ $order->getTotal() }}</td>
                                <td>
                                    @if ($order->getPaymentMethod() == 'cash_on_delivery')
                                        <span class="badge bg-primary">{{ __('Cash on Delivery') }}</span>
                                    @else
                                        <span class="badge bg-success">{{ __('Online Payment') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($order->getPaymentStatus() == 'pending')
                                        <span class="badge bg-warning">{{ __('Pending') }}</span>
                                    @elseif($order->getPaymentStatus() == 'paid')
                                        <span class="badge bg-success">{{ __('Paid') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Failed') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.order.show', $order->getId()) }}" class="btn btn-info btn-sm">
                                        <i class="bi-eye"></i> {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $viewData['orders']->links() }}
            </div>
        </div>
    </div>
@endsection
