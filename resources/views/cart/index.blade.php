@extends('layouts.app')
@section('title', $viewData['title'])
@section('subtitle', $viewData['subtitle'])
@section('content')
    <div class="card">
        <div class="card-header">
            {{ __('Products in Cart') }}
            <span class="badge bg-primary ms-2" id="cart-count">{{ $viewData['itemCount'] }}</span>
        </div>
        <div class="card-body">
            @if (count($viewData['products']) > 0)
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('ID') }}</th>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Price') }}</th>
                            <th scope="col">{{ __('Quantity') }}</th>
                            <th scope="col">{{ __('Subtotal') }}</th>
                            <th scope="col">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($viewData['products'] as $product)
                            <tr>
                                <td>{{ $product->getId() }}</td>
                                <td>{{ $product->getName() }}</td>
                                <td>${{ $product->getPrice() }}</td>
                                <td>
                                    <form action="{{ route('cart.update', $product->getId()) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="number" name="quantity"
                                            value="{{ $viewData['cartItems'][$product->getId()] }}" min="1"
                                            max="{{ $product->getQuantity_store() }}"
                                            class="form-control form-control-sm d-inline-block" style="width: 80px;">
                                        <button type="submit" class="btn btn-sm btn-outline-primary ms-1">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>${{ $product->getPrice() * $viewData['cartItems'][$product->getId()] }}</td>
                                <td>
                                    <form action="{{ route('cart.remove', $product->getId()) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('{{ __('Are you sure you want to remove this item?') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <div class="text-end">
                        <a class="btn btn-outline-secondary mb-2"><b>{{ __('Total to pay') }}:</b>
                            ${{ $viewData['total'] }}</a>
                        <a href="{{ route('payment.options') }}"
                            class="btn bg-primary text-white mb-2">{{ __('Proceed to Payment') }}</a>
                        <a href="{{ route('cart.delete') }}">
                            <button class="btn btn-danger mb-2">
                                {{ __('Remove all products from Cart') }}
                            </button>
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('Your cart is empty') }}</h5>
                    <p class="text-muted">{{ __('Add some products to get started!') }}</p>
                    <a href="{{ route('product.index') }}" class="btn btn-primary">
                        {{ __('Continue Shopping') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Fonction pour mettre à jour le compteur du panier
            function updateCartCount() {
                fetch('{{ route('api.cart.count') }}')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('cart-count').textContent = data.count;
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Mettre à jour le compteur au chargement de la page
            document.addEventListener('DOMContentLoaded', function() {
                updateCartCount();
            });

            // Validation des quantités
            document.querySelectorAll('input[name="quantity"]').forEach(input => {
                input.addEventListener('change', function() {
                    const max = parseInt(this.getAttribute('max'));
                    const value = parseInt(this.value);

                    if (value > max) {
                        alert('{{ __('The requested quantity exceeds available stock.') }}');
                        this.value = max;
                    } else if (value < 1) {
                        alert('{{ __('Quantity must be at least 1.') }}');
                        this.value = 1;
                    }
                });
            });
        </script>
    @endpush
@endsection
