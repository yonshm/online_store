<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $viewData = [];
        $viewData["title"] = "Products - Online Store";
        $viewData["subtitle"] = "List of products";

        // Check if the select value is sent via GET or POST
        $filter = $request->input('filter'); // e.g., 'discounted', 'all', etc.

        if ($filter === 'discounted') {
            $products = Product::all()->filter(function ($product) {
                return method_exists($product, 'getDiscountedPrice') && $product->getDiscountedPrice() < $product->getPrice();
            });
            // Convert collection to paginator for discounted products
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $products->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 12),
                $products->count(),
                12,
                \Illuminate\Pagination\Paginator::resolveCurrentPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
        } else {
            $products = Product::paginate(12);
        }

        $viewData["products"] = $products;
        return view('product.index')->with("viewData", $viewData);
    }

    public function show($id)
    {
        $viewData = [];
        $product = Product::findOrFail($id);
        $viewData["title"] = $product->getName() . " - Online Store";
        $viewData["subtitle"] =  $product->getName() . " - Product information";
        $viewData["product"] = $product;
        return view('product.show')->with("viewData", $viewData);
    }
}
