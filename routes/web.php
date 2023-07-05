<?php

use App\Http\Controllers\CatalogProductController;
use App\Http\Controllers\CompanyBranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
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
Route::post('catalog-products/update-with-media/{catalog_product}', [CatalogProductController::class, 'updateWithMedia'])->name('catalog-products.update-with-media');


// ------- Ventas(Clients Routes)  ---------
Route::resource('companies', CompanyController::class)->middleware('auth');
Route::post('companies/massive-delete', [CompanyController::class, 'massiveDelete'])->name('companies.massive-delete');
Route::post('companies/clone', [CompanyController::class, 'clone'])->name('companies.clone');


// ------- Ventas(sale orders Routes)  ---------
Route::resource('sales', SaleController::class)->middleware('auth');
Route::post('sales/massive-delete', [SaleController::class, 'massiveDelete'])->name('sales.massive-delete');
Route::post('sales/clone', [SaleController::class, 'clone'])->name('sales.clone');


// ------- Ventas(Companybranches sucursales Routes)  ---------
Route::resource('company-branches', CompanyBranchController::class)->middleware('auth');


// ------- Compras(Suppliers Routes)  ---------
Route::resource('suppliers', SupplierController::class)->middleware('auth');
Route::post('suppliers/massive-delete', [SupplierController::class, 'massiveDelete'])->name('suppliers.massive-delete');


// ------- Compras(purchases Routes)  ---------
Route::resource('purchases', PurchaseController::class)->middleware('auth');

// ------- Nóminas(Payrolls Routes)  ---------
Route::post('payrolls/processed-attendances', [PayrollController::class, 'getProcessedAttendances'])->middleware('auth')->name('payrolls.processed-attendances');
Route::post('payrolls/handle-late', [PayrollController::class, 'handleLate'])->middleware('auth')->name('payrolls.handle-late');
Route::post('payrolls/handle-extras', [PayrollController::class, 'handleExtras'])->middleware('auth')->name('payrolls.handle-extras');

// ------- Raw Material routes  ---------
Route::resource('raw-materials', RawMaterialController::class)->middleware('auth');
Route::post('raw-materials/massive-delete', [RawMaterialController::class, 'massiveDelete'])->name('raw-materials.massive-delete')->middleware('auth');
Route::get('consumables/create', [RawMaterialController::class, 'create'])->name('consumables.create')->middleware('auth');
Route::get('consumables-edit/{raw_material}', [RawMaterialController::class, 'editConsumable'])->name('consumables.edit')->middleware('auth');


// ------- Almacen routes---------
Route::get('/storage-raw-materials', [StorageController::class, 'index'])->middleware('auth')->name('storages.raw-materials.index');
Route::get('/storage-consumables', [StorageController::class, 'index'])->middleware('auth')->name('storages.consumables.index');
Route::get('/storage-finished-products', [StorageController::class, 'index'])->middleware('auth')->name('storages.finished-products.index');
Route::get('/storage-finished-products/create', [StorageController::class, 'create'])->middleware('auth')->name('storages.finished-products.create');
Route::get('/storage-finished-products/{storage}/edit', [StorageController::class, 'edit'])->middleware('auth')->name('storages.finished-products.edit');
Route::post('/storage/store', [StorageController::class, 'store'])->middleware('auth')->name('storages.store');
Route::get('/storage-scraps', [StorageController::class, 'index'])->middleware('auth')->name('storages.scraps.index');
Route::get('/storage-scraps/create', [StorageController::class, 'create'])->middleware('auth')->name('storages.scraps.create');
Route::post('/storage-scraps/store', [StorageController::class, 'scrapStore'])->middleware('auth')->name('storages.scraps.store');
Route::post('storages/scrap/massive-delete', [StorageController::class, 'scrapMassiveDelete'])->name('storages.scraps.massive-delete');
Route::post('storages/massive-delete', [StorageController::class, 'massiveDelete'])->name('storages.massive-delete');


// ------- Recursos humanos(Payrolls Routes)  ---------
Route::resource('payrolls', PayrollController::class)->middleware('auth');


// ------- Recursos humanos(users routes)  ---------
Route::resource('users', UserController::class)->middleware('auth');


// ------- Machines Routes  ---------
Route::resource('machines', MachineController::class)->middleware('auth');
Route::post('machines/massive-delete', [MachineController::class, 'massiveDelete'])->name('machines.massive-delete');


// ------- Maintenances routes  ---------
Route::resource('maintenances', MaintenanceController::class)->except('create')->middleware('auth');
Route::get('maintenances/create/{selectedMachine}',[ MaintenanceController::class, 'create'])->name('maintenances.create')->middleware('auth');


// ------- Maintenances routes  ---------
Route::resource('spare-parts', SparePartController::class)->except('create')->middleware('auth');
Route::get('spare-parts/create/{selectedMachine}',[ SparePartController::class, 'create'])->name('spare-parts.create')->middleware('auth');


Route::post('/upload-image',[FileUploadController::class, 'upload'])->name('upload-image');

