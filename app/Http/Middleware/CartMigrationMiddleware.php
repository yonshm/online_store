<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\CartService;

class CartMigrationMiddleware
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier s'il y a des données de session à migrer
        $sessionProducts = $request->session()->get('products');

        if ($sessionProducts && !$request->cookie('shopping_cart')) {
            // Migrer les données de session vers les cookies
            $cartItems = $this->cartService->getCartItems($request);

            // Fusionner les données de session avec les cookies existants
            foreach ($sessionProducts as $productId => $quantity) {
                if (isset($cartItems[$productId])) {
                    $cartItems[$productId] += $quantity;
                } else {
                    $cartItems[$productId] = $quantity;
                }
            }

            // Sauvegarder dans les cookies
            $cookie = $this->cartService->saveCart($cartItems);

            // Supprimer les données de session
            $request->session()->forget('products');

            // Ajouter le cookie à la réponse
            $response = $next($request);
            return $response->withCookie($cookie);
        }

        return $next($request);
    }
}
