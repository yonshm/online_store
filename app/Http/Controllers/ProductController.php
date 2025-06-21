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
            $products = Product::all()->filter(function($product) {
                return method_exists($product, 'getDiscountedPrice') && $product->getDiscountedPrice() < $product->getPrice();
            });
        } else {
            $products = Product::all();
        }

        $viewData["products"] = $products;
        return view('product.index')->with("viewData", $viewData);
    }

    public function show($id)
    {
        $viewData = [];
        $product = Product::findOrFail($id);
        $viewData["title"] = $product->getName()." - Online Store";
        $viewData["subtitle"] =  $product->getName()." - Product information";
        $viewData["product"] = $product;
        return view('product.show')->with("viewData", $viewData);
    }
}
