<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Product;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $total = 0;
        $productsInCart = [];

        $productsInSession = $request->session()->get("products");
        if ($productsInSession) {
            $productsInCart = Product::findMany(array_keys($productsInSession));
            $total = Product::sumPricesByQuantities($productsInCart, $productsInSession);
        }

        $viewData = [];
        $viewData["title"] = "Cart - Online Store";
        $viewData["subtitle"] =  "Shopping Cart";
        $viewData["total"] = $total;
        $viewData["products"] = $productsInCart;
        return view('cart.index')->with("viewData", $viewData);
    }

    public function add(Request $request, $id)
    {
        $products = $request->session()->get("products");
        
        $product = Product::findOrFail($id);
        $productQuantity = (int) $request->input('quantity');

        if($productQuantity > $product->getQuantity_store()){
            return redirect()->back()->with('error','La quantité demandée dépasse le stock disponible.');
        }
        $products[$id] = $productQuantity;

        $request->session()->put('products', $products);

        return redirect()->route('cart.index');
    }

    public function delete(Request $request)
    {
        $request->session()->forget('products');
        return back();
    }

 public function purchase(Request $request)
{
    $productsInSession = $request->session()->get("products");
    if (!$productsInSession) {
        return redirect()->route('cart.index');
    }

    $userId = Auth::user()->getId();
    $order = new Order();
    $order->setUserId($userId);
    $order->setTotal(0);
    $order->save();

    $total = 0;
    $now = now();

    $productsInCart = Product::findMany(array_keys($productsInSession));

    $globalDiscounts = Discount::where('type', 'global')
        ->where('start_date', '<=', $now)
        ->where('end_date', '>=', $now)
        ->get();

    foreach ($productsInCart as $product) {
        $quantity = $productsInSession[$product->getId()];

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

    $request->session()->forget('products');

    $viewData = [
        "title" => "Purchase - Online Store",
        "subtitle" => "Purchase Status",
        "order" => $order,
    ];

    return view('cart.purchase')->with("viewData", $viewData);
}
}
