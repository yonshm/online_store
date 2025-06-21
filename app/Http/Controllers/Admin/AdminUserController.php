<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{

    public function index()
    {
        $admins = User::where('role', 'admin')->paginate(10);
        return view('superAdmin.admin.index', compact('admins'));
    }

    public function create()
    {
        return view('superAdmin.admin.create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);


        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return redirect()->route('superAdmin.users.index')->with('success', 'Admin créé avec succès');
    }
    public function edit($id)
    {
        $admin = User::findOrFail($id);
        return view('superAdmin.admin.edit', compact('admin'));
    }
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|min:6',
        ]);

        $admin->name = $request->name;
        $admin->email = $request->email;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        return redirect()->route('superAdmin.users.index')->with('success', 'Admin mis à jour avec succès');
    }
    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();
        return redirect()->route('superAdmin.users.index')->with('success', 'Admin supprimé avec succès');
    }
}
