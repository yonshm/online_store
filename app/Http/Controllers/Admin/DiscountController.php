<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    // Display a list of all discounts
    public function index()
    {
        $discounts = Discount::all();
        $products = Product::all();
        $categories = Category::all();
        return view('admin.discounts.index', compact('discounts','products','categories'));
    }

    // Show the form to create a new discount
    public function create()
    {
        // Get all categories and products to display in the form
        $categories = Category::all();
        $products = Product::all();

        return view('admin.discounts.create', compact('categories', 'products'));
    }

    // Store a new discount
    public function store(Request $request)
    {
        // Validate the discount data
        $request->validate([
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'type' => 'required|in:product,category,global', // Specify the type (product or category)
            'category_id' => 'nullable|exists:categories,id', // Only required for category type
            'product_id' => 'nullable|exists:products,id',   // Only required for product type
        ]);

        // Create the discount
        Discount::create([
            'discount_percentage' => $request->discount_percentage,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'product_id' => $request->product_id,
        ]);

        return redirect()->route('discounts.index')->with('success', 'Discount created successfully');
    }

    // Show the form to edit an existing discount
    public function edit($id)
    {
        $discount = Discount::findOrFail($id);
        $categories = Category::all();
        $products = Product::all();

        return view('admin.discounts.edit', compact('discount', 'categories', 'products'));
    }

    // Update an existing discount
    public function update(Request $request, $id)
    {
        // Validate the discount data
        $request->validate([
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'type' => 'required|in:product,category,global',
            'category_id' => 'nullable|exists:categories,id',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $discount = Discount::findOrFail($id);

        $discount->update([
            'discount_percentage' => $request->discount_percentage,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'category_id' => $request->category_id,
            'product_id' => $request->product_id,
        ]);

        return redirect()->route('discounts.index')->with('success', 'Discount updated successfully');
    }

    // Delete a discount
    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return redirect()->route('discounts.index')->with('success', 'Discount deleted successfully');
    }
}
