<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CartService
{
    private const CART_COOKIE_NAME = 'shopping_cart';
    private const CART_COOKIE_EXPIRY = 60 * 24 * 30; // 30 jours

    /**
     * Récupérer les produits du panier depuis les cookies
     */
    public function getCartItems(Request $request): array
    {
        $cartData = $request->cookie(self::CART_COOKIE_NAME);

        if (!$cartData) {
            return [];
        }

        $cartItems = json_decode($cartData, true);

        return is_array($cartItems) ? $cartItems : [];
    }

    /**
     * Ajouter un produit au panier
     */
    public function addToCart(Request $request, int $productId, int $quantity): array
    {
        $cartItems = $this->getCartItems($request);

        // Vérifier si le produit existe déjà dans le panier
        if (isset($cartItems[$productId])) {
            $cartItems[$productId] += $quantity;
        } else {
            $cartItems[$productId] = $quantity;
        }

        return $cartItems;
    }

    /**
     * Mettre à jour la quantité d'un produit dans le panier
     */
    public function updateCartItem(Request $request, int $productId, int $quantity): array
    {
        $cartItems = $this->getCartItems($request);

        if ($quantity <= 0) {
            unset($cartItems[$productId]);
        } else {
            $cartItems[$productId] = $quantity;
        }

        return $cartItems;
    }

    /**
     * Supprimer un produit du panier
     */
    public function removeFromCart(Request $request, int $productId): array
    {
        $cartItems = $this->getCartItems($request);
        unset($cartItems[$productId]);

        return $cartItems;
    }

    /**
     * Vider le panier
     */
    public function clearCart(): array
    {
        return [];
    }

    /**
     * Sauvegarder le panier dans les cookies
     */
    public function saveCart(array $cartItems): \Symfony\Component\HttpFoundation\Cookie
    {
        $cartData = json_encode($cartItems);

        return Cookie::make(
            self::CART_COOKIE_NAME,
            $cartData,
            self::CART_COOKIE_EXPIRY,
            '/',
            null,
            false, // secure
            false, // httpOnly
            false, // raw
            'Lax' // sameSite
        );
    }

    /**
     * Supprimer le cookie du panier
     */
    public function deleteCartCookie(): \Symfony\Component\HttpFoundation\Cookie
    {
        return Cookie::forget(self::CART_COOKIE_NAME);
    }

    /**
     * Récupérer les produits avec leurs détails depuis la base de données
     */
    public function getCartProducts(array $cartItems): array
    {
        if (empty($cartItems)) {
            return [];
        }

        return Product::findMany(array_keys($cartItems));
    }

    /**
     * Calculer le total du panier
     */
    public function calculateTotal(array $cartItems, array $products): float
    {
        if (empty($cartItems) || empty($products)) {
            return 0;
        }

        return Product::sumPricesByQuantities($products, $cartItems);
    }

    /**
     * Vérifier la disponibilité du stock pour tous les produits du panier
     */
    public function validateStock(array $cartItems): array
    {
        $errors = [];

        foreach ($cartItems as $productId => $quantity) {
            $product = Product::find($productId);

            if (!$product) {
                $errors[] = "Le produit avec l'ID {$productId} n'existe plus.";
                continue;
            }

            if ($quantity > $product->getQuantity_store()) {
                $errors[] = "Le produit '{$product->getName()}' n'a que {$product->getQuantity_store()} unités en stock, vous en avez demandé {$quantity}.";
            }
        }

        return $errors;
    }

    /**
     * Obtenir le nombre total d'articles dans le panier
     */
    public function getCartItemCount(Request $request): int
    {
        $cartItems = $this->getCartItems($request);
        return array_sum($cartItems);
    }

    /**
     * Vérifier si le panier est vide
     */
    public function isCartEmpty(Request $request): bool
    {
        return empty($this->getCartItems($request));
    }

    /**
     * Obtenir les informations détaillées du panier
     */
    public function getCartDetails(Request $request): array
    {
        $cartItems = $this->getCartItems($request);
        $products = $this->getCartProducts($cartItems);
        $total = $this->calculateTotal($cartItems, $products);
        $itemCount = $this->getCartItemCount($request);

        return [
            'items' => $cartItems,
            'products' => $products,
            'total' => $total,
            'itemCount' => $itemCount,
            'isEmpty' => empty($cartItems)
        ];
    }
}
