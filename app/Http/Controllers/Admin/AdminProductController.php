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
        $viewData['products'] = Product::paginate(10);
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
        $query = Product::with(['category', 'supplier']);

        if ($categoryId && $categoryId != -1) {
            $query->where('category_id', $categoryId);
        }
        if ($supplierId && $supplierId != -1) {
            $query->where('supplier_id', $supplierId);
        }
        $products = $query->paginate(10);
        return response()->json($products);
    }

    public function exportCsv()
    {
        $products = Product::with(['category', 'supplier'])->get();

        $filename = 'products_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'ID',
                'Name',
                'Description',
                'Price',
                'Image',
                'Category ID',
                'Category Name',
                'Quantity Store',
                'Supplier ID',
                'Supplier Name',
                'Created At',
                'Updated At'
            ]);

            // Données des produits
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->getId(),
                    $product->getName(),
                    $product->getDescription(),
                    $product->getPrice(),
                    $product->getImage(),
                    $product->getCategoryId(),
                    $product->category ? $product->category->name : 'N/A',
                    $product->getQuantity_store(),
                    $product->supplier_id,
                    $product->supplier ? $product->supplier->raison_social : 'N/A',
                    $product->created_at,
                    $product->updated_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $imported = 0;
        $errors = [];

        if (($handle = fopen($path, 'r')) !== FALSE) {
            // Ignorer l'en-tête
            fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== FALSE) {
                try {
                    // Vérifier si le produit existe déjà
                    $existingProduct = Product::where('name', $data[1])->first();

                    if ($existingProduct) {
                        // Mettre à jour le produit existant
                        $existingProduct->setDescription($data[2]);
                        $existingProduct->setPrice($data[3]);
                        $existingProduct->setImage($data[4]);
                        $existingProduct->setCategoryId($data[5]);
                        $existingProduct->setQuantity_store($data[7]);
                        $existingProduct->supplier_id = $data[8];
                        $existingProduct->save();
                    } else {
                        // Créer un nouveau produit
                        $product = new Product();
                        $product->setName($data[1]);
                        $product->setDescription($data[2]);
                        $product->setPrice($data[3]);
                        $product->setImage($data[4]);
                        $product->setCategoryId($data[5]);
                        $product->setQuantity_store($data[7]);
                        $product->supplier_id = $data[8];
                        $product->save();
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Erreur à la ligne " . ($imported + 2) . ": " . $e->getMessage();
                }
            }
            fclose($handle);
        }

        $message = "Import terminé. $imported produits traités.";
        if (!empty($errors)) {
            $message .= " Erreurs: " . implode(', ', $errors);
        }

        return back()->with('import_message', $message);
    }
}
