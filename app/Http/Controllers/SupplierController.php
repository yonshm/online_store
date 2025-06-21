<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // Liste des fournisseurs
    public function index()
    {
        $suppliers = Supplier::all();

        return view('admin.supplier.index', ['suppliers' => $suppliers]);
    }

    // Affichage du formulaire de création
    public function create()
    {
        return view('admin.supplier.create');
    }

    // Enregistrer un nouveau fournisseur
    public function store(Request $request)
    {
        $request->validate([
            'raison_social' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'tele' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string',
        ]);

        Supplier::create($request->all());

        return redirect()->route('adminSuppliers.index')->with('success', 'Fournisseur ajouté avec succès');
    }

    // Affichage d'un fournisseur (optionnel)
    public function show(Supplier $supplier)
    {
        return view('supplier.show', compact('supplier'));
    }

    // Formulaire d'édition
    public function edit(Supplier $adminSupplier)
{
    return view('admin.supplier.edit', ['supplier' => $adminSupplier]);
}

public function update(Request $request, Supplier $adminSupplier)
{

    // dd($request->all());
    $request->validate([
        'raison_social' => 'required|string|max:255',
        'adresse' => 'nullable|string|max:255',
        'tele' => 'nullable|string|max:50',
        'email' => 'nullable|email|max:255',
        'description' => 'nullable|string',
    ]);

    $adminSupplier->update($request->all());

    return redirect()->route('adminSuppliers.index')->with('success', 'Fournisseur modifié avec succès');
}


    public function destroy($id)
{
    $supplier = Supplier::findOrFail($id);
    $supplier->delete();

    return redirect()->route('adminSuppliers.index')->with('success', 'Fournisseur supprimé avec succès.');
}

}
