<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use App\Models\Discount;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $cartDetails = $this->cartService->getCartDetails($request);

        $viewData = [];
        $viewData["title"] = "Cart - Online Store";
        $viewData["subtitle"] = "Shopping Cart";
        $viewData["total"] = $cartDetails['total'];
        $viewData["products"] = $cartDetails['products'];
        $viewData["cartItems"] = $cartDetails['items'];
        $viewData["itemCount"] = $cartDetails['itemCount'];

        return view('cart.index')->with("viewData", $viewData);
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $productQuantity = (int) $request->input('quantity');

        if ($productQuantity <= 0) {
            return redirect()->back()->with('error', 'La quantité doit être supérieure à 0.');
        }

        if ($productQuantity > $product->getQuantity_store()) {
            return redirect()->back()->with('error', 'La quantité demandée dépasse le stock disponible.');
        }

        // Ajouter au panier
        $cartItems = $this->cartService->addToCart($request, $id, $productQuantity);

        // Sauvegarder dans les cookies
        $cookie = $this->cartService->saveCart($cartItems);

        return redirect()->route('cart.index')->withCookie($cookie);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $productQuantity = (int) $request->input('quantity');

        if ($productQuantity > $product->getQuantity_store()) {
            return redirect()->back()->with('error', 'La quantité demandée dépasse le stock disponible.');
        }

        // Mettre à jour le panier
        $cartItems = $this->cartService->updateCartItem($request, $id, $productQuantity);

        // Sauvegarder dans les cookies
        $cookie = $this->cartService->saveCart($cartItems);

        return redirect()->route('cart.index')->withCookie($cookie);
    }

    public function remove(Request $request, $id)
    {
        // Supprimer du panier
        $cartItems = $this->cartService->removeFromCart($request, $id);

        // Sauvegarder dans les cookies
        $cookie = $this->cartService->saveCart($cartItems);

        return redirect()->route('cart.index')->withCookie($cookie);
    }

    public function delete(Request $request)
    {
        $cookie = $this->cartService->deleteCartCookie();
        return redirect()->route('cart.index')->withCookie($cookie);
    }

    public function purchase(Request $request)
    {
        $cartItems = $this->cartService->getCartItems($request);

        if (empty($cartItems)) {
            return redirect()->route('cart.index');
        }

        // Valider le stock avant l'achat
        $stockErrors = $this->cartService->validateStock($cartItems);
        if (!empty($stockErrors)) {
            return redirect()->route('cart.index')->with('error', implode(' ', $stockErrors));
        }

        $userId = Auth::user()->getId();
        $order = new Order();
        $order->setUserId($userId);
        $order->setTotal(0);
        $order->save();

        $total = 0;
        $now = now();

        $productsInCart = $this->cartService->getCartProducts($cartItems);

        $globalDiscounts = Discount::where('type', 'global')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        foreach ($productsInCart as $product) {
            $quantity = $cartItems[$product->getId()];

            $relevantDiscounts = Discount::where(function ($query) use ($product) {
                $query->where('type', 'category')
                    ->where('category_id', $product->category_id)
                    ->orWhere(function ($q) use ($product) {
                        $q->where('type', 'product')
                            ->where('product_id', $product->id);
                    });
            })
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->get();

            $activeDiscounts = $globalDiscounts->merge($relevantDiscounts);

            $discountTotal = $activeDiscounts->sum('discount_percentage');
            $discountTotal = min($discountTotal, 90);
            $originalPrice = $product->getPrice();
            $finalPrice = round($originalPrice * (1 - $discountTotal / 100), 2);

            $item = new Item();
            $item->setQuantity($quantity);
            $item->setPrice($finalPrice);
            $item->setProductId($product->getId());
            $item->setOrderId($order->getId());
            $item->save();

            $total += $finalPrice * $quantity;
            $product->quantity_store = $product->getQuantity_store() - $quantity;
            $product->save();
        }

        $order->setTotal($total);
        $order->save();

        $user = Auth::user();
        $newBalance = $user->getBalance() - $total;
        $user->setBalance($newBalance);
        $user->save();

        // Vider le panier après l'achat
        $cookie = $this->cartService->deleteCartCookie();

        $viewData = [
            "title" => "Purchase - Online Store",
            "subtitle" => "Purchase Status",
            "order" => $order,
        ];

        return view('cart.purchase')->with("viewData", $viewData)->withCookie($cookie);
    }

    /**
     * API endpoint pour obtenir le nombre d'articles dans le panier
     */
    public function getCartCount(Request $request)
    {
        $itemCount = $this->cartService->getCartItemCount($request);

        return response()->json([
            'count' => $itemCount,
            'isEmpty' => $this->cartService->isCartEmpty($request)
        ]);
    }

    /**
     * API endpoint pour obtenir les détails du panier
     */
    public function getCartDetails(Request $request)
    {
        $cartDetails = $this->cartService->getCartDetails($request);

        return response()->json($cartDetails);
    }
}
