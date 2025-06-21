<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Item;
use App\Models\Product;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function showPaymentOptions(Request $request)
    {
        $productsInSession = $request->session()->get("products");
        if (!$productsInSession) {
            return redirect()->route('cart.index');
        }

        $total = 0;
        $productsInCart = Product::findMany(array_keys($productsInSession));
        $now = now();

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

            $total += $finalPrice * $quantity;
        }

        $viewData = [
            "title" => "Payment Options - Online Store",
            "subtitle" => "Choose Payment Method",
            "total" => $total,
            "products" => $productsInCart,
            "quantities" => $productsInSession
        ];

        return view('payment.options')->with("viewData", $viewData);
    }

    public function processCashOnDelivery(Request $request)
    {
        $productsInSession = $request->session()->get("products");
        if (!$productsInSession) {
            return redirect()->route('cart.index');
        }

        $userId = Auth::user()->getId();
        $order = new Order();
        $order->setUserId($userId);
        $order->setTotal(0);
        $order->setPaymentMethod('cash_on_delivery');
        $order->setPaymentStatus('pending');
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

        $request->session()->forget('products');

        $viewData = [
            "title" => "Order Confirmed - Online Store",
            "subtitle" => "Cash on Delivery Order",
            "order" => $order,
            "paymentMethod" => "Cash on Delivery"
        ];

        return view('payment.confirmation')->with("viewData", $viewData);
    }

    public function processOnlinePayment(Request $request)
    {
        $productsInSession = $request->session()->get("products");
        if (!$productsInSession) {
            return redirect()->route('cart.index');
        }

        $userId = Auth::user()->getId();
        $order = new Order();
        $order->setUserId($userId);
        $order->setTotal(0);
        $order->setPaymentMethod('online');
        $order->setPaymentStatus('pending');
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

        // Simuler un paiement en ligne réussi
        $user = Auth::user();
        $newBalance = $user->getBalance() - $total;
        $user->setBalance($newBalance);
        $user->save();

        $order->setPaymentStatus('paid');
        $order->save();

        $request->session()->forget('products');

        $viewData = [
            "title" => "Payment Successful - Online Store",
            "subtitle" => "Online Payment Completed",
            "order" => $order,
            "paymentMethod" => "Online Payment"
        ];

        return view('payment.confirmation')->with("viewData", $viewData);
    }
}
