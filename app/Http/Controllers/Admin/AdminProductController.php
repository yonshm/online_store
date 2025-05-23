<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    public function index()
    {
        $viewData = [];
        $viewData['title'] = 'Admin Page - Products - Online Store';
        $viewData['products'] = Product::all();
        $viewData['categories'] = Category::all();
        

        $viewData['suppliers'] = Supplier::all();
        // dd($viewData["suppliers"]);
        return view('admin.product.index')->with('viewData', $viewData);
    }

    public function store(Request $request)
    {
        Product::validate($request);

        $newProduct = new Product();
        $newProduct->setName($request->input('name'));
        $newProduct->setDescription($request->input('description'));
        $newProduct->setPrice($request->input('price'));
        $newProduct->setImage('game.png');
        $newProduct->setCategoryId($request->input('category_id'));
        $newProduct->setQuantity_store($request->input('quantity_store'));
        $newProduct->supplier_id = $request->input('supplier_id');

        $newProduct->save();

        if ($request->hasFile('image')) {
            $imageName = $newProduct->getId() . '.' . $request->file('image')->extension();
            Storage::disk('public')->put($imageName, file_get_contents($request->file('image')->getRealPath()));
            $newProduct->setImage($imageName);
            $newProduct->save();
        }

        return back();
    }

    public function delete($id)
    {
        Product::destroy($id);
        return back();
    }

    public function edit($id)
    {
        $viewData = [];
        $viewData['title'] = 'Admin Page - Edit Product - Online Store';
        $viewData['product'] = Product::with('supplier')->findOrFail($id);
        $viewData['categories'] = Category::all();
        $viewData['suppliers'] = Supplier::all();
        // dd($viewData['product']);

        return view('admin.product.edit')->with('viewData', $viewData);
    }

    public function update(Request $request, $id)
    {
        Product::validate($request);

        $product = Product::findOrFail($id);
        $product->setName($request->input('name'));
        $product->setDescription($request->input('description'));
        $product->setPrice($request->input('price'));
        $product->setQuantity_store($request->input('quantity_store'));
        $product->supplier_id = $request->input('supplier_id');
        

        if ($request->hasFile('image')) {
            $imageName = $product->getId() . '.' . $request->file('image')->extension();
            Storage::disk('public')->put($imageName, file_get_contents($request->file('image')->getRealPath()));
            $product->setImage($imageName);
        }

        $product->save();
        return redirect()->route('admin.product.index');
    }

    public function filter(Request $request)
    {

        $categoryId = $request->input('category_id');
        $supplierId = $request->input('supplier_id');
        $query = Product::with(['category','supplier']);

        if($categoryId && $categoryId !=-1){
            $query->where('category_id', $categoryId);
        }
        if($supplierId && $supplierId != -1){
            $query->where('supplier_id', $supplierId);
        }
        $products = $query->get();
        return response()->json($products);
    }
}
