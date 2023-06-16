<?php

use App\Http\Controllers\CatalogProductController;
use App\Http\Controllers\CompanyBranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Models\CatalogProduct;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


// ------- Catalog Products Routes ---------
Route::resource('catalog-products', CatalogProductController::class)->middleware('auth');
Route::post('catalog-products/massive-delete', [CatalogProductController::class, 'massiveDelete'])->name('catalog-products.massive-delete');


// ------- Ventas(Clients Routes)  ---------
Route::resource('companies', CompanyController::class)->middleware('auth');
Route::post('companies/massive-delete', [CompanyController::class, 'massiveDelete'])->name('companies.massive-delete');



// ------- Ventas(sale orders Routes)  ---------
Route::resource('sales', SaleController::class)->middleware('auth');


// ------- Ventas(Companybranches sucursales Routes)  ---------
Route::resource('company-branches', CompanyBranchController::class)->middleware('auth');


// ------- Compras(Suppliers Routes)  ---------
Route::resource('suppliers', SupplierController::class)->middleware('auth');


// ------- Compras(purchases Routes)  ---------
Route::resource('purchases', PurchaseController::class)->middleware('auth');

