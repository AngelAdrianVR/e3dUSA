<?php

use App\Http\Controllers\AdditionalTimeRequestController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\CatalogProductController;
use App\Http\Controllers\CompanyBranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\KioskDeviceController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProductionCostController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SampleController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
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
Route::put('sales/authorize/{sale}', [SaleController::class, 'authorizeOrder'])->name('sales.authorize');


// ------- Ventas(Companybranches sucursales Routes)  ---------
Route::resource('company-branches', CompanyBranchController::class)->middleware('auth');


// ------- Compras(Suppliers Routes)  ---------
Route::resource('suppliers', SupplierController::class)->middleware('auth');
Route::post('suppliers/massive-delete', [SupplierController::class, 'massiveDelete'])->name('suppliers.massive-delete');


// ------- Compras(purchases Routes)  ---------
Route::resource('purchases', PurchaseController::class)->middleware('auth');
Route::post('purchases/massive-delete', [PurchaseController::class, 'massiveDelete'])->name('purchases.massive-delete');
Route::post('purchases/clone', [PurchaseController::class, 'clone'])->name('purchases.clone');
Route::put('purchases/mark-order-done/{currentPurchase}', [PurchaseController::class, 'markOrderDone'])->name('purchases.done');
Route::put('purchases/mark-order-recieved/{currentPurchase}', [PurchaseController::class, 'markOrderRecieved'])->name('purchases.recieved');

// ------- Raw Material routes  ---------
Route::resource('raw-materials', RawMaterialController::class)->middleware('auth');
Route::post('raw-materials/massive-delete', [RawMaterialController::class, 'massiveDelete'])->name('raw-materials.massive-delete')->middleware('auth');
Route::get('consumables/create', [RawMaterialController::class, 'create'])->name('consumables.create')->middleware('auth');
Route::get('consumables-edit/{raw_material}', [RawMaterialController::class, 'editConsumable'])->name('consumables.edit')->middleware('auth');

// ------- Recursos humanos(Payrolls Routes)  ---------
Route::resource('payrolls', PayrollController::class)->middleware('auth');
Route::post('payrolls/processed-attendances', [PayrollController::class, 'getProcessedAttendances'])->middleware('auth')->name('payrolls.processed-attendances');
Route::post('payrolls/handle-late', [PayrollController::class, 'handleLate'])->middleware('auth')->name('payrolls.handle-late');
Route::post('payrolls/handle-extras', [PayrollController::class, 'handleExtras'])->middleware('auth')->name('payrolls.handle-extras');
Route::post('payrolls/handle-incidents', [PayrollController::class, 'handleIncidents'])->middleware('auth')->name('payrolls.handle-incidents');
Route::post('payrolls/handle-attendance', [PayrollController::class, 'handleAttendance'])->middleware('auth')->name('payrolls.handle-attendance');
Route::post('payrolls/update-attendances', [PayrollController::class, 'updateAttendances'])->middleware('auth')->name('payrolls.update-attendances');
Route::post('payrolls/get-payroll', [PayrollController::class, 'getPayroll'])->middleware('auth')->name('payrolls.get-payroll');
Route::post('payrolls/get-bonuses', [PayrollController::class, 'getBonuses'])->middleware('auth')->name('payrolls.get-bonuses');
Route::post('payrolls/get-extras', [PayrollController::class, 'getExtras'])->middleware('auth')->name('payrolls.get-extras');
Route::post('payrolls/get-payroll-users', [PayrollController::class, 'getPayrollUsers'])->middleware('auth')->name('payrolls.get-payroll-users');
Route::post('payrolls/print-template', [PayrollController::class, 'printTemplate'])->middleware('auth')->name('payrolls.print-template');
Route::post('payrolls/get-additional-time', [PayrollController::class, 'getAdditionalTime'])->middleware('auth')->name('payrolls.get-additional-time');

// ------- Recursos humanos(users routes)  ---------
Route::resource('users', UserController::class)->middleware('auth');
Route::get('users-get-next-attendance', [UserController::class, 'getNextAttendance'])->middleware('auth')->name('users.get-next-attendance');
Route::get('users-set-attendance', [UserController::class, 'setAttendance'])->middleware('auth')->name('users.set-attendance');

// ------- Recursos humanos(Roles and permissions Routes)  ---------
Route::get('role-permission', [RolePermissionController::class, 'index'])->middleware('auth')->name('role-permission.index');
Route::put('role-permission/{role}/edit-role', [RolePermissionController::class, 'updateRole'])->middleware('auth')->name('role-permission.update-role');
Route::post('role-permission/store-role', [RolePermissionController::class, 'storeRole'])->middleware('auth')->name('role-permission.store-role');
Route::delete('role-permission/{role}/destroy-role', [RolePermissionController::class, 'deleteRole'])->middleware('auth')->name('role-permission.delete-role');
Route::put('role-permission/{permission}/edit-permission', [RolePermissionController::class, 'updatePermission'])->middleware('auth')->name('role-permission.update-permission');
Route::post('role-permission/store-permission', [RolePermissionController::class, 'storePermission'])->middleware('auth')->name('role-permission.store-permission');
Route::delete('role-permission/{permission}/destroy-permission', [RolePermissionController::class, 'deletePermission'])->middleware('auth')->name('role-permission.delete-permission');

// ------- Recursos humanos(Bonuses Routes)  ---------
Route::resource('bonuses', BonusController::class)->middleware('auth');
Route::post('bonuses/massive-delete', [BonusController::class, 'massiveDelete'])->name('bonuses.massive-delete');

// ------- Recursos humanos(Holidays Routes)  ---------
Route::resource('holidays', HolidayController::class)->middleware('auth');
Route::post('holidays/massive-delete', [HolidayController::class, 'massiveDelete'])->name('holidays.massive-delete');

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
Route::get('storages/{storage}/show', [StorageController::class, 'show'])->name('storages.show');
Route::delete('storages/{storage}/destroy', [StorageController::class, 'destroy'])->name('storages.destroy');
Route::post('storages/{storage}/add-storage', [StorageController::class, 'addStorage'])->name('storages.add');
Route::post('storages/{storage}/sub-storage', [StorageController::class, 'subStorage'])->name('storages.sub');

// ----------------MUESTRAS-----------------------
Route::resource('samples', SampleController::class)->middleware('auth');
Route::post('samples/massive-delete', [SampleController::class, 'massiveDelete'])->name('samples.massive-delete');
Route::put('samples/returned-sample/{sample}', [SampleController::class, 'returned'])->name('samples.returned');

// ------- Design department routes  ---------
Route::resource('designs', DesignController::class)->middleware('auth');
Route::post('designs/massive-delete', [DesignController::class, 'massiveDelete'])->name('designs.massive-delete');
Route::put('designs/start-order/{design}', [DesignController::class, 'startOrder'])->name('designs.start-order');
Route::post('designs/finish-order/{design}', [DesignController::class, 'finishOrder'])->name('designs.finish-order');

// ------- production department routes  ---------
Route::resource('productions', ProductionController::class)->middleware('auth');
Route::post('productions/massive-delete', [ProductionController::class, 'massiveDelete'])->name('productions.massive-delete');
Route::get('productions/print/{productions}', [ProductionController::class, 'print'])->name('productions.print');
Route::put('productions/change-status/{production}', [ProductionController::class, 'changeStatus'])->name('productions.change-status');

// ------- Machines Routes  ---------
Route::resource('machines', MachineController::class)->middleware('auth');
Route::post('machines/massive-delete', [MachineController::class, 'massiveDelete'])->name('machines.massive-delete');
Route::post('machines/upload-files/{machine}', [MachineController::class, 'uploadFiles'])->name('machines.upload-files');


// ------- aditional time request Routes  ---------
Route::resource('more-additional-times', AdditionalTimeRequestController::class)->middleware('auth');
Route::post('more-additional-times/massive-delete', [AdditionalTimeRequestController::class, 'massiveDelete'])->name('more-additional-times.massive-delete');
Route::get('admin-additional-times', [AdditionalTimeRequestController::class, 'adminIndex'])->name('admin-additional-times.index');
Route::put('admin-additional-times/authorize/{admin_additional_time}', [AdditionalTimeRequestController::class, 'authorizeRequest'])->name('admin-additional-times.authorize');
Route::put('admin-additional-times/unauthorize/{admin_additional_time}', [AdditionalTimeRequestController::class, 'unauthorizeRequest'])->name('admin-additional-times.unauthorize');
Route::post('admin-additional-times/massive-delete', [AdditionalTimeRequestController::class, 'massiveDeleteAdmin'])->name('admin-additional-times.massive-delete');



// ------- Maintenances routes  -------------
Route::resource('maintenances', MaintenanceController::class)->except('create')->middleware('auth');
Route::get('maintenances/create/{selectedMachine}',[ MaintenanceController::class, 'create'])->name('maintenances.create')->middleware('auth');


// ---------- spare parts routes  ---------------
Route::resource('spare-parts', SparePartController::class)->except('create')->middleware('auth');
Route::get('spare-parts/create/{selectedMachine}',[ SparePartController::class, 'create'])->name('spare-parts.create')->middleware('auth');


//------------------ Meetings routes ----------------
Route::resource('meetings', MeetingController::class)->middleware('auth');
Route::post('meetings/massive-delete', [MeetingController::class, 'massiveDelete'])->name('meetings.massive-delete');


//------------------ Meetings routes ----------------
Route::resource('production-costs', ProductionCostController::class)->middleware('auth');
Route::post('production-costs/massive-delete', [ProductionCostController::class, 'massiveDelete'])->name('production-costs.massive-delete');

//------------------ Kiosk routes ----------------
Route::post('kiosk', [KioskDeviceController::class, 'store'])->name('kiosk.store');

Route::post('/upload-image',[FileUploadController::class, 'upload'])->name('upload-image');

