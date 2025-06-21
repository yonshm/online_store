<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\LanguageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Language switching route
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

Route::get('/', 'App\Http\Controllers\HomeController@index')->name("home.index");
Route::get('/about', 'App\Http\Controllers\HomeController@about')->name("home.about");
Route::get('/products', 'App\Http\Controllers\ProductController@index')->name("product.index");
Route::get('/products/{id}', 'App\Http\Controllers\ProductController@show')->name("product.show");

Route::get('/cart', 'App\Http\Controllers\CartController@index')->name("cart.index");
Route::get('/cart/delete', 'App\Http\Controllers\CartController@delete')->name("cart.delete");
Route::post('/cart/add/{id}', 'App\Http\Controllers\CartController@add')->name("cart.add");

Route::middleware('auth')->group(function () {
    Route::get('/cart/purchase', 'App\Http\Controllers\CartController@purchase')->name("cart.purchase");
    Route::get('/my-account/orders', 'App\Http\Controllers\MyAccountController@orders')->name("myaccount.orders");
});

Route::middleware('admin')->group(function () {

    Route::get('/admin', 'App\Http\Controllers\Admin\AdminHomeController@index')->name("admin.home.index");
    Route::get('/admin/dashboard/pdf', 'App\Http\Controllers\Admin\AdminHomeController@downloadPdf')->name("admin.dashboard.pdf");

    // Products Routes
    Route::get('/admin/products', 'App\Http\Controllers\Admin\AdminProductController@index')->name("admin.product.index");
    Route::post('/admin/products/store', 'App\Http\Controllers\Admin\AdminProductController@store')->name("admin.product.store");
    Route::delete('/admin/products/{id}/delete', 'App\Http\Controllers\Admin\AdminProductController@delete')->name("admin.product.delete");
    Route::get('/admin/products/{id}/edit', 'App\Http\Controllers\Admin\AdminProductController@edit')->name("admin.product.edit");
    Route::put('/admin/products/{id}/update', 'App\Http\Controllers\Admin\AdminProductController@update')->name("admin.product.update");


    // Categories Routes
    Route::get('/admin/categories', [AdminCategoryController::class, 'index'])->name("admin.category.index");
    Route::resource('adminCategories',AdminCategoryController::class);
    Route::get('/admin/products/filter', [AdminProductController::class, 'filter'])->name('admin.product.filter');


    // discounts routes
    Route::resource('discounts', DiscountController::class);

    Route::get('admin/adminSuppliers', [SupplierController::class , 'index'])->name("admin.supplier.index");
    Route::resource('adminSuppliers', SupplierController::class);
});

    Route::get('/superAdmin/users', [AdminUserController::class, 'index'])->name('superAdmin.users.index');
    Route::get('/superAdmin/users/create', [AdminUserController::class, 'create'])->name('superAdmin.users.create');
    Route::post('/superAdmin/users/store', [AdminUserController::class, 'store'])->name('superAdmin.users.store');
    Route::get('/superAdmin/users/{id}/edit', [AdminUserController::class, 'edit'])->name('superAdmin.users.edit');
    Route::put('/superAdmin/users/{id}/update', [AdminUserController::class, 'update'])->name('superAdmin.users.update');
    Route::delete('/superAdmin/users/{id}/delete', [AdminUserController::class, 'destroy'])->name('superAdmin.users.delete');


Auth::routes();
