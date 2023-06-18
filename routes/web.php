<?php

use App\Http\Controllers\CatalogProductController;
use App\Http\Controllers\CompanyBranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StorageController;
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
Route::post('catalog-products/clone', [CatalogProductController::class, 'clone'])->name('catalog-products.clone');


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


// ------- Raw Material routes  ---------
Route::resource('raw-materials', RawMaterialController::class)->middleware('auth');
Route::post('raw-materials/massive-delete', [RawMaterialController::class, 'massiveDelete'])->name('raw-materials.massive-delete')->middleware('auth');
Route::get('consumables/create', [RawMaterialController::class, 'create'])->name('consumables.create')->middleware('auth');
Route::get('consumables-edit/{raw_material}', [RawMaterialController::class, 'editConsumable'])->name('consumables.edit')->middleware('auth');



// ------- Almacen routes---------
Route::get('/storage-raw-materials', [StorageController::class, 'index'])->middleware('auth')->name('storages.raw-materials.index');
Route::get('/storage-consumables', [StorageController::class, 'index'])->middleware('auth')->name('storages.consumables.index');
Route::get('/storage-finished-products', [StorageController::class, 'index'])->middleware('auth')->name('storages.finished-products.index');
Route::get('/storage-scraps', [StorageController::class, 'index'])->middleware('auth')->name('storages.scraps.index');
Route::post('storages/massive-delete', [StorageController::class, 'massiveDelete'])->name('storages.massive-delete');




Route::post('/upload-image',[FileUploadController::class, 'upload'])->name('upload-image');

