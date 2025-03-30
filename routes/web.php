<?php

use App\Http\Controllers\AdditionalTimeRequestController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CallMonitorController;
use App\Http\Controllers\CatalogProductCompanySaleController;
use App\Http\Controllers\CatalogProductController;
use App\Http\Controllers\ClientMonitorController;
use App\Http\Controllers\CompanyBranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerMeetingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignAuthorizationController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\DesignModificationController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\EmailMonitorController;
use App\Http\Controllers\ExclusiveDesignController;
use App\Http\Controllers\ExtraTimeRequestController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\KioskDeviceController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MettingMonitorController;
use App\Http\Controllers\OportunityController;
use App\Http\Controllers\OportunityTaskController;
use App\Http\Controllers\PaymentMonitorController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProductionCostController;
use App\Http\Controllers\ProductionProgressController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectGroupController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SaleAnaliticController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\ShippingRateController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsappMonitorController;
use App\Models\CompanyBranch;
use App\Models\RawMaterial;
use App\Models\Supplier;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('register-keychains', function () {
    // editar productos de proveedor con id 9
    $supplier = Supplier::find(9);
    $products = $supplier->raw_materials_id;

    // obtener toda la materia prima que pertenece a la categoria de llaveros
    $keychains = RawMaterial::where('part_number', 'LIKE', 'LL-%')->get(['id'])->pluck('id');
    
    // registrar todo a productos del proveedor
    $products = array_merge($products,$keychains->toArray());
    $supplier->update(['raw_materials_id' => $products]);

    return 'Todos los llaveros registrados!';
});

Route::get('/inicio', function () {
    return Inertia::render('Auth/Inicio');
})->name('inicio');

Route::redirect('/', 'inicio');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// prueba de pdf OC
// Route::get('oc-pdf', [PurchaseController::class, 'pdfTest']);


// -------------- project groups routes ------------
Route::resource('project-groups', ProjectGroupController::class)->middleware('auth')->names('project-groups');

// -------------- extra time requests routes ------------
Route::resource('extra-time-requests', ExtraTimeRequestController::class)->middleware('auth')->names('extra-time-requests');
Route::put('extra-time-requests/{etr}/set-response', [ExtraTimeRequestController::class, 'setResponse'])->middleware('auth')->name('extra-time-requests.set-response');

// -------------- tags routes ------------
Route::resource('tags', TagController::class)->middleware('auth')->names('tags');

// --------------- Calendar routes -----------------
Route::resource('calendars', CalendarController::class)->middleware('auth');
Route::put('calendars-{calendar}-task-done', [CalendarController::class, 'taskDone'])->name('calendars.task-done')->middleware('auth');
Route::put('calendars/set-attendance-confirmation/{calendar}', [CalendarController::class, 'SetAttendanceConfirmation'])->name('calendars.set-attendance-confirmation');

Route::get('customers-report', function () {
    $customerWithSales = CompanyBranch::withCount(['sales' => function ($query) {
        // Filtra las ventas con fecha de creación de hace un año hasta hoy
        $query->whereBetween('created_at', [now()->subYear(), now()]);
    }])->get();

    $data = $customerWithSales->filter(function ($cliente) {
        return $cliente->sales_count > 0;
    });

    return inertia('Sale/Report', compact('data'));
})->name('sales.customers-report');

// ----------- Search routes --------
Route::get('/search', [SearchController::class, 'index'])->name('search');


// ------- Catalog Products Routes ---------
Route::post('catalog-product-company-sale/store-traveler-data/{cpcs}', [CatalogProductCompanySaleController::class, 'storeTravelerData'])->middleware('auth')->name('catalog-product-company-sale.store-traveler-data');
Route::get('catalog-product-company-sale/get-traveler-data/{cpcs}', [CatalogProductCompanySaleController::class, 'getTravelerData'])->middleware('auth')->name('catalog-product-company-sale.get-traveler-data');
Route::get('catalog-product-company-sale/get-productions/{cpcs}', [CatalogProductCompanySaleController::class, 'getProductions'])->middleware('auth')->name('catalog-product-company-sale.get-productions');
Route::get('catalog-product-company-sale/get-raw-materials/{cpcs}', [CatalogProductCompanySaleController::class, 'getRawMaterials'])->middleware('auth')->name('catalog-product-company-sale.get-raw-materials');
Route::get('catalog-product-company-sale/get-estimated-completion-date/{cpcs}', [CatalogProductCompanySaleController::class, 'getEstimatedCompletionDate'])->middleware('auth')->name('catalog-product-company-sale.get-estimated-completion-date');


// ------- Catalog Products Routes ---------
Route::resource('catalog-products', CatalogProductController::class)->middleware('auth');
Route::post('catalog-products/massive-delete', [CatalogProductController::class, 'massiveDelete'])->name('catalog-products.massive-delete');
Route::post('catalog-products/clone', [CatalogProductController::class, 'clone'])->name('catalog-products.clone');
Route::post('catalog-products/update-with-media/{catalog_product}', [CatalogProductController::class, 'updateWithMedia'])->name('catalog-products.update-with-media');
Route::post('catalog-products/QR-search-catalog-product', [CatalogProductController::class, 'QRSearchCatalogProduct'])->name('catalog-products.QR-search-catalog-product');
Route::get('catalog-products/{catalog_product}/get-data', [CatalogProductController::class, 'getCatalogProductData'])->name('catalog-products.get-data');
Route::get('catalog-products-fetch-shipping-rates/{catalog_product}', [CatalogProductController::class, 'fetchShippingRates'])->name('catalog-products.fetch-shipping-rates');
Route::get('catalog-products-prices-report', [CatalogProductController::class, 'pricesReport'])->name('catalog-products.prices-report');
Route::post('catalog-products-get-by-ids', [CatalogProductController::class, 'getByIds'])->name('catalog-products.get-by-ids');
Route::get('catalog-products/{catalog_product}/get-info', [CatalogProductController::class, 'getInfo'])->name('catalog-products.get-info');


// ------- Ventas(Clients Routes)  ---------
Route::resource('companies', CompanyController::class)->middleware('auth');
Route::post('companies/massive-delete', [CompanyController::class, 'massiveDelete'])->name('companies.massive-delete');
Route::post('companies/clone', [CompanyController::class, 'clone'])->name('companies.clone');
Route::post('companies/get-all-companies', [CompanyController::class, 'getAllCompanies'])->name('companies.get-all-companies')->middleware('auth');
Route::get('companies-get-exclusive-designs/{company}', [CompanyController::class, 'getExclusiveDesigns'])->name('companies.get-exclusive-designs')->middleware('auth');
Route::get('companies-contacts-report', [CompanyController::class, 'contactsReport'])->name('companies.contacts-report')->middleware('auth');
Route::post('companies-attach-catalog-product', [CompanyController::class, 'attachCatalogProduct'])->name('companies.attach-catalog-product')->middleware('auth');


// ------- shippings routes  ---------
Route::resource('shippings', ShippingController::class)->middleware('auth');
Route::post('shippings/massive-delete', [ShippingController::class, 'massiveDelete'])->name('shippings.massive-delete');
Route::post('shippings-get-matches', [ShippingController::class, 'getMatches'])->name('shippings.get-matches');
Route::get('shippings-index-all', [ShippingController::class, 'indexAll'])->name('shippings.index-all');
Route::get('shippings-fetch-filtered/{filter}', [ShippingController::class, 'fetchFiltered'])->name('shippings.fetch-filtered');
Route::post('shippings-update-status/{shipping}', [ShippingController::class, 'updateStatus'])->name('shippings.update-status');
Route::post('shippings-partiality-sent/{shipping}', [ShippingController::class, 'updateSent'])->name('shippings.partiality-sent');
Route::post('shippings-costs-report', [ShippingController::class, 'costsReport'])->name('shippings.costs-report');
Route::get('shippings/fetch-catalog-product-info/{catalog_product}', [ShippingController::class, 'fetchCatalogProductInfo'])->name('shippings.fetch-catalog-product-info');


// ------- boxes routes  ---------
Route::resource('boxes', BoxController::class)->middleware('auth');
Route::post('boxes/massive-delete', [BoxController::class, 'massiveDelete'])->name('boxes.massive-delete');


// ------- shipping-rates routes  ---------
Route::resource('shipping-rates', ShippingRateController::class)->middleware('auth');
Route::post('shipping-rates/massive-delete', [ShippingRateController::class, 'massiveDelete'])->name('shipping-rates.massive-delete');
Route::post('shipping-rates-get-matches', [ShippingRateController::class, 'getMatches'])->name('shipping-rates.get-matches');
Route::get('shipping-rates/fetch-catalog-product-info/{catalog_product}', [ShippingRateController::class, 'fetchCatalogProductInfo'])->name('shipping-rates.fetch-catalog-product-info');


// ------- CRM (Clients Routes)  ---------
Route::get('crm', [DashboardController::class, 'crmDashboard'])->middleware('auth')->name('crm.dashboard');


// ------- CRM (exclusive customer designs Routes)  ---------
Route::resource('exclusive-designs', ExclusiveDesignController::class)->middleware('auth');


// ------- CRM (oportunities Routes)  ---------
Route::resource('oportunities', OportunityController::class)->middleware('auth');
Route::put('/oportunities/update-status/{oportunity_id}', [OportunityController::class, 'updateStatus'])->name('oportunities.update-status')->middleware('auth');
Route::put('/oportunities/create-sale/{oportunity_id}', [OportunityController::class, 'createSale'])->name('oportunities.create-sale')->middleware('auth');
Route::post('oportunities/update-with-media/{oportunity}', [OportunityController::class, 'updateWithMedia'])->name('oportunities.update-with-media')->middleware('auth');


// ------- CRM (surveys Routes)  ---------
Route::get('/surveys/create/{oportunity_id}', [SurveyController::class, 'create'])->name('surveys.create');
Route::post('/surveys/store/{oportunity_id}', [SurveyController::class, 'store'])->name('surveys.store');


// ------- CRM (oportunityTasks Routes)  ---------
Route::resource('oportunity-tasks', OportunityTaskController::class)->except(['store', 'create'])->middleware('auth');
Route::get('oportunity-tasks/create/{oportunity_id}', [OportunityTaskController::class, 'create'])->name('oportunity-tasks.create')->middleware('auth');
Route::post('oportunity-tasks/store/{oportunity_id}', [OportunityTaskController::class, 'store'])->name('oportunity-tasks.store')->middleware('auth');
Route::post('oportunity-tasks/{oportunity_task}/comment', [OportunityTaskController::class, 'comment'])->name('oportunity-tasks.comment')->middleware('auth');
Route::put('oportunity-tasks/mark-as-done/{oportunityTask}', [OportunityTaskController::class, 'markAsDone'])->name('oportunity-tasks.mark-as-done')->middleware('auth');


// ------- CRM (prospectos Routes)  ---------
Route::resource('prospects', ProspectController::class)->middleware('auth');
Route::get('prospects-get-matches/{query}', [ProspectController::class, 'getMatches'])->name('prospects.get-matches');
Route::get('prospects-get-quotes/{prospect}', [ProspectController::class, 'getQuotes'])->name('prospects.get-quotes');
Route::post('prospects/turn-into-customer/{prospect}', [ProspectController::class, 'turnIntoCustomer'])->name('prospects.turn-into-customer');


// ------- CRM (Client monior Routes)  ---------
Route::resource('client-monitors', ClientMonitorController::class)->middleware('auth');


// ------- CRM (Payment monior Routes)  ---------
Route::resource('payment-monitors', PaymentMonitorController::class)->middleware('auth');
Route::post('payment-monitors/update-with-media/{payment_monitor}', [PaymentMonitorController::class, 'updateWithMedia'])->name('payment-monitors.update-with-media')->middleware('auth');


// ------- CRM (meeting monior Routes)  ---------
Route::resource('meeting-monitors', MettingMonitorController::class)->middleware('auth');


// ------- CRM (email monior Routes)  ---------
Route::resource('email-monitors', EmailMonitorController::class)->middleware('auth');


// ------- CRM (Call monior Routes)  ---------
Route::resource('call-monitors', CallMonitorController::class)->middleware('auth');


// ------- CRM (whatsapp monior Routes)  ---------
Route::resource('whatsapp-monitors', WhatsappMonitorController::class)->middleware('auth');
Route::post('whatsapp-monitors/update-with-media/{whatsapp_monitor}', [WhatsappMonitorController::class, 'updateWithMedia'])->name('whatsapp-monitors.update-with-media')->middleware('auth');


// ------- CRM(sale orders Routes)  ---------
Route::resource('sales', SaleController::class)->middleware('auth');
Route::post('sales/massive-delete', [SaleController::class, 'massiveDelete'])->name('sales.massive-delete');
Route::post('sales/clone', [SaleController::class, 'clone'])->name('sales.clone');
Route::put('sales/authorize/{sale}', [SaleController::class, 'authorizeOrder'])->name('sales.authorize');
Route::get('sales/print/{sale}', [SaleController::class, 'print'])->name('sales.print');
Route::post('sales/update-with-media/{sale}', [SaleController::class, 'updateWithMedia'])->name('sales.update-with-media')->middleware('auth');
Route::get('sales-get-unauthorized', [SaleController::class, 'getUnauthorized'])->name('sales.get-unauthorized');
Route::get('sales-get-matches/{query}', [SaleController::class, 'getMatches'])->name('sales.get-matches');
Route::get('sales-quality-certificate/{sale_id}', [SaleController::class, 'QualityCertificate'])->name('sales.quality-certificate');
Route::get('sales-fetch-filtered/{filter}', [SaleController::class, 'fetchFiltered'])->name('sales.fetch-filtered');
Route::get('sales-check-if-has-sale/{catalog_prroduct_company_id}', [SaleController::class, 'checkIfHasSale'])->name('sales.check-if-has-sale');


// ------- CRM(Companybranches sucursales Routes)  ---------
Route::resource('company-branches', CompanyBranchController::class)->middleware('auth');
Route::put('company-branches/clear-important-notes/{company_branch}', [CompanyBranchController::class, 'clearImportantNotes'])->name('company-branches.clear-important-notes')->middleware('auth');
Route::put('company-branches/store-important-notes/{company_branch}', [CompanyBranchController::class, 'storeImportantNotes'])->name('company-branches.store-important-notes')->middleware('auth');
Route::put('company-branches/update-product-price/{product_company}', [CompanyBranchController::class, 'updateProductPrice'])->name('company-branches.update-product-price')->middleware('auth');
Route::get('company-branches/fetch-design-info/{company_branch}', [CompanyBranchController::class, 'fetchDesignInfo'])->name('company-branches.fetch-design-info')->middleware('auth');


// ------- Compras(Suppliers Routes)  ---------
Route::resource('suppliers', SupplierController::class)->middleware('auth');
Route::get('fetch-supplier/{supplier_id}', [SupplierController::class, 'fetchSupplier'])->name('suppliers.fetch-supplier');
Route::get('supplier-get-orders/{supplier}', [SupplierController::class, 'getOrders'])->name('suppliers.get-orders');
Route::get('supplier-rating-report/{p}', [SupplierController::class, 'ratingReport'])->name('suppliers.rating-report');
Route::post('suppliers/massive-delete', [SupplierController::class, 'massiveDelete'])->name('suppliers.massive-delete');
Route::post('suppliers/clone', [SupplierController::class, 'clone'])->name('suppliers.clone');


// ------- Compras(purchases Routes)  ---------
Route::resource('purchases', PurchaseController::class)->middleware('auth');
Route::get('purchases-show-template/{purchase_id}', [PurchaseController::class, 'showTemplate'])->name('purchases.show-template')->middleware('auth');
Route::post('purchases/massive-delete', [PurchaseController::class, 'massiveDelete'])->name('purchases.massive-delete');
Route::post('purchases/clone', [PurchaseController::class, 'clone'])->name('purchases.clone');
Route::post('purchases/send-email/{purchase}', [PurchaseController::class, 'sendEmail'])->name('purchases.send-email');
Route::put('purchases/mark-order-done/{currentPurchase}', [PurchaseController::class, 'markOrderDone'])->name('purchases.done');
Route::put('purchases/mark-order-recieved/{currentPurchase}', [PurchaseController::class, 'markOrderRecieved'])->name('purchases.recieved');
Route::put('purchases/authorize/{purchase}', [PurchaseController::class, 'authorizePurchase'])->name('purchases.authorize');
Route::put('purchases/update-quantity/{purchase}', [PurchaseController::class, 'updateQuantity'])->name('purchases.update-quantity');
Route::put('purchases/store-rating/{purchase}', [PurchaseController::class, 'storeRating'])->name('purchases.store-rating');
// Route::get('develop-template', [PurchaseController::class, 'developTemplate'])->name('develop.template');


//-------------- Projects routes ------------------
Route::resource('projects', ProjectController::class)->middleware('auth');
Route::get('projects-dashboard', [ProjectController::class, 'dashboard'])->middleware('auth')->name('projects.dashboard');
Route::post('projects/update-with-media/{project}', [ProjectController::class, 'updateWithMedia'])->name('projects.update-with-media')->middleware('auth');
Route::get('projects-get-matches/{query}', [ProjectController::class, 'getMatches'])->name('projects.get-matches');


//-------------------------- Tasks routes -------------------------
Route::resource('tasks', TaskController::class)->middleware('auth');
Route::post('tasks-{task}-comment', [TaskController::class, 'comment'])->name('tasks.comment')->middleware('auth');
Route::put('tasks-{task}-pause-play', [TaskController::class, 'pausePlayTask'])->name('tasks.pause-play')->middleware('auth');
Route::put('tasks-{task}-update-status', [TaskController::class, 'updateStatus'])->name('tasks.update-status')->middleware('auth');
Route::get('tasks-late-tasks', [TaskController::class, 'getLateTasks'])->middleware('auth')->name('tasks.get-late-tasks');


// ------- Raw Material routes  ---------
Route::resource('raw-materials', RawMaterialController::class)->middleware('auth');
Route::post('raw-materials/massive-delete', [RawMaterialController::class, 'massiveDelete'])->name('raw-materials.massive-delete')->middleware('auth');
Route::post('raw-materials/turn-into-catalog-product', [RawMaterialController::class, 'turnIntoCatalogProduct'])->name('raw-materials.turn-into-catalog-product')->middleware('auth');
Route::post('raw-materials/update-with-media/{raw_material}', [RawMaterialController::class, 'updateWithMedia'])->name('raw-materials.update-with-media')->middleware('auth');
Route::get('raw-materials/fetch/{raw_material_id}', [RawMaterialController::class, 'fetchItem'])->name('raw-materials.fetch')->middleware('auth');
Route::get('raw-materials-fetch-supplier-items/{raw_materials_ids}', [RawMaterialController::class, 'fetchSupplierItems'])->name('raw-materials.fetch-supplier-items')->middleware('auth');
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
Route::post('payrolls/get-discounts', [PayrollController::class, 'getDiscounts'])->middleware('auth')->name('payrolls.get-discounts');
Route::post('payrolls/get-extras', [PayrollController::class, 'getExtras'])->middleware('auth')->name('payrolls.get-extras');
Route::post('payrolls/get-extras-requests', [PayrollController::class, 'getExtrasRequests'])->middleware('auth')->name('payrolls.get-extras-requests');
Route::post('payrolls/get-payroll-users', [PayrollController::class, 'getPayrollUsers'])->middleware('auth')->name('payrolls.get-payroll-users');
Route::post('payrolls/get-additional-time', [PayrollController::class, 'getAdditionalTime'])->middleware('auth')->name('payrolls.get-additional-time');
Route::post('payrolls/close-current', [PayrollController::class, 'closeCurrent'])->middleware('auth')->name('payrolls.close-current');
Route::post('payrolls/get-current-payroll', [PayrollController::class, 'getCurrentPayroll'])->middleware('auth')->name('payrolls.get-current-payroll');
Route::post('payrolls/get-all-payrolls', [PayrollController::class, 'getAllPayrolls'])->middleware('auth')->name('payrolls.get-all-payrolls');
Route::get('payrolls/print-template/{users_id_to_show}/{payroll_id}', [PayrollController::class, 'printTemplate'])->middleware('auth')->name('payrolls.print-template');
Route::get('payrolls/get-users/{payroll_id}', [PayrollController::class, 'getUsers'])->middleware('auth')->name('payrolls.get-users');

// ------- Recursos humanos(users routes)  ---------
Route::resource('users', UserController::class)->middleware('auth');
Route::get('users-get-next-attendance', [UserController::class, 'getNextAttendance'])->middleware('auth')->name('users.get-next-attendance');
Route::get('users-get-pause-status', [UserController::class, 'getPauseStatus'])->middleware('auth')->name('users.get-pause-status');
Route::get('users-set-attendance', [UserController::class, 'setAttendance'])->middleware('auth')->name('users.set-attendance');
Route::get('users-set-pause', [UserController::class, 'setPause'])->middleware('auth')->name('users.set-pause');
Route::get('users-get-additional-time-requested-days/{user_id}/{payroll_id}', [UserController::class, 'getRequestedDays'])->middleware('auth')->name('users.get-additional-time-requested-days');
Route::get('users-get-pendent-tasks', [UserController::class, 'getPendentTasks'])->middleware('auth')->name('users.get-pendent-tasks');
Route::put('users-reset-pass/{user}', [UserController::class, 'resetPass'])->middleware('auth')->name('users.reset-pass');
Route::put('users-change-status/{user}', [UserController::class, 'changeStatus'])->middleware('auth')->name('users.change-status');
Route::put('users-update-pausas/{payroll_user}', [UserController::class, 'updatePausas'])->middleware('auth')->name('users.update-pausas');
Route::put('users-update-vacations/{user}', [UserController::class, 'updateVacations'])->middleware('auth')->name('users.update-vacations');
Route::post('users-get-unseen-messages', [UserController::class, 'getUnseenMessages'])->middleware('auth')->name('users.get-unseen-messages');
Route::post('users-get-notifications', [UserController::class, 'getNotifications'])->middleware('auth')->name('users.get-notifications');
Route::post('users-read-notifications', [UserController::class, 'readNotifications'])->middleware('auth')->name('users.read-notifications');
Route::post('users-delete-notifications', [UserController::class, 'deleteNotifications'])->middleware('auth')->name('users.delete-notifications');
Route::get('users-get-all', [UserController::class, 'getAllUsers'])->middleware('auth')->name('users.get-all');
Route::get('users-get-operators', [UserController::class, 'getOperators'])->middleware('auth')->name('users.get-operators');

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


// ------- Recursos humanos(Discounts Routes)  ---------
Route::resource('discounts', DiscountController::class)->middleware('auth');
Route::post('discounts/massive-delete', [DiscountController::class, 'massiveDelete'])->name('discounts.massive-delete');

// ------- Recursos humanos(Holidays Routes)  ---------
Route::resource('holidays', HolidayController::class)->middleware('auth');
Route::post('holidays/massive-delete', [HolidayController::class, 'massiveDelete'])->name('holidays.massive-delete');

// ------- Almacen routes---------
Route::get('/storage-raw-materials', [StorageController::class, 'index'])->middleware('auth')->name('storages.raw-materials.index');
Route::get('/storage-consumables', [StorageController::class, 'index'])->middleware('auth')->name('storages.consumables.index');
Route::get('/storage-finished-products', [StorageController::class, 'index'])->middleware('auth')->name('storages.finished-products.index');
Route::get('/storage-obsolete', [StorageController::class, 'index'])->middleware('auth')->name('storages.obsolete.index');
Route::get('/storage-sample', [StorageController::class, 'index'])->middleware('auth')->name('storages.samples.index');
Route::get('/storage-finished-products/create', [StorageController::class, 'create'])->middleware('auth')->name('storages.finished-products.create');
Route::get('/storage-finished-products/{storage}/edit', [StorageController::class, 'edit'])->middleware('auth')->name('storages.finished-products.edit');
Route::get('/storage-obsolete/create', [StorageController::class, 'create'])->middleware('auth')->name('storages.obsolete.create');
Route::get('/storage-obsolete/{storage}/edit', [StorageController::class, 'edit'])->middleware('auth')->name('storages.obsolete.edit');
Route::post('/storage-obsolete/store', [StorageController::class, 'storeObsolete'])->middleware('auth')->name('storages.obsolete.store');
Route::post('/storage-samples/store', [StorageController::class, 'storeSamples'])->middleware('auth')->name('storages.samples.store');
Route::get('/storage-obsolete-show/{storage}', [StorageController::class, 'showObsolete'])->middleware('auth')->name('storages.obsolete.show');
Route::get('/storage-sample/create', [StorageController::class, 'create'])->middleware('auth')->name('storages.samples.create');
Route::get('/storage-sample/{storage}/edit', [StorageController::class, 'edit'])->middleware('auth')->name('storages.samples.edit');
Route::post('/storage-store', [StorageController::class, 'store'])->middleware('auth')->name('storages.store');
Route::get('/storage-scraps', [StorageController::class, 'index'])->middleware('auth')->name('storages.scraps.index');
Route::get('/storage-scraps/create', [StorageController::class, 'create'])->middleware('auth')->name('storages.scraps.create');
Route::post('/storage-scraps/store', [StorageController::class, 'scrapStore'])->middleware('auth')->name('storages.scraps.store');
Route::post('storages/scrap/massive-delete', [StorageController::class, 'scrapMassiveDelete'])->name('storages.scraps.massive-delete');
Route::post('storages/sample/massive-delete', [StorageController::class, 'sampleMassiveDelete'])->name('storages.samples.massive-delete');
Route::post('storages/massive-delete', [StorageController::class, 'massiveDelete'])->name('storages.massive-delete');
Route::get('storages/{storage}/show', [StorageController::class, 'show'])->name('storages.show');
Route::get('/storage-show-consumables/{storage}', [StorageController::class, 'showConsumable'])->middleware('auth')->name('storages.consumables.show');
Route::delete('storages/{storage}/destroy', [StorageController::class, 'destroy'])->name('storages.destroy');
Route::post('storages/{storage}/add-storage', [StorageController::class, 'addStorage'])->name('storages.add');
Route::post('storages/{storage}/sub-storage', [StorageController::class, 'subStorage'])->name('storages.sub');
Route::post('storages/QR-storage', [StorageController::class, 'QRStorage'])->name('storages.QR');
Route::post('storages/QR-search-product', [StorageController::class, 'QRSearchProduct'])->name('storages.QR-search-product');
Route::put('storages/{storage}/reactivate-obsolete', [StorageController::class, 'reactivateObsolete'])->name('storages.reactivate-obsolete');

// ----------------MUESTRAS-----------------------
Route::resource('samples', SampleController::class)->middleware('auth');
Route::post('samples/massive-delete', [SampleController::class, 'massiveDelete'])->name('samples.massive-delete');
Route::put('samples/returned-sample/{sample}', [SampleController::class, 'returned'])->name('samples.returned');
Route::put('samples/sale-order-sample/{sample}', [SampleController::class, 'saleOrder'])->name('samples.sale-order');
Route::put('samples/finish-sample/{sample}', [SampleController::class, 'finishSample'])->name('samples.finish-sample');
Route::put('samples/resent-sample/{sample}', [SampleController::class, 'resentSample'])->name('samples.resent-sample');
Route::post('samples/update-with-media/{sample}', [SampleController::class, 'updateWithMedia'])->name('samples.update-with-media')->middleware('auth');
Route::put('samples/authorize/{sample}', [SampleController::class, 'authorizeSample'])->name('samples.authorize');
Route::post('samples/get-matches', [SampleController::class, 'getMatches'])->name('samples.get-matches');

// ------- Design department routes  ---------
Route::resource('designs', DesignController::class)->middleware('auth');
Route::post('designs/massive-delete', [DesignController::class, 'massiveDelete'])->name('designs.massive-delete');
Route::put('designs/start-order/{design}', [DesignController::class, 'startOrder'])->name('designs.start-order');
Route::post('designs/finish-order', [DesignController::class, 'finishOrder'])->name('designs.finish-order');
Route::put('designs/authorize/{design}', [DesignController::class, 'authorizeOrder'])->name('designs.authorize');
Route::post('designs/update-with-media/{design}', [DesignController::class, 'updateWithMedia'])->name('designs.update-with-media');
Route::get('designs-fetch-filtered/{filter}', [DesignController::class, 'fetchFiltered'])->name('designs.fetch-filtered');
Route::get('designs-get-by-id/{id}', [DesignController::class, 'getById'])->name('designs.get-by-id');
Route::get('designs-activities-report/{p}', [DesignController::class, 'activitiesReport'])->name('designs.activities-report');

// ------- Design modifications routes  ---------
Route::resource('design-modifications', DesignModificationController::class)->middleware('auth');
Route::post('design-modifications/upload-results', [DesignModificationController::class, 'uploadResults'])->name('design-modifications.upload-results');


// ------- Design autorizations routes  ---------
Route::resource('design-authorizations', DesignAuthorizationController::class)->middleware('auth');
Route::put('design-authorizations-authorize/{design_authorization}', [DesignAuthorizationController::class, 'AuthorizeDesign'])->name('design-authorizations.authorize')->middleware('auth');
Route::post('design-authorizations/update-with-media/{design_authorization}', [DesignAuthorizationController::class, 'updateWithMedia'])->name('design-authorizations.update-with-media');
Route::get('design-authorizations/fetch-for-company-brach/{company_branch_id}', [DesignAuthorizationController::class, 'fetchForCompanyBranch'])->name('design-authorizations.fetch-for-company-branch');
Route::get('design-authorizations-print/{design_authorization}', [DesignAuthorizationController::class, 'print'])->name('design-authorizations.print');


// ------- production department routes  ---------
Route::resource('productions', ProductionController::class)->middleware('auth');
Route::get('productions-admin-index', [ProductionController::class, 'adminIndex'])->name('productions.admin-index');
Route::post('productions/massive-delete', [ProductionController::class, 'massiveDelete'])->name('productions.massive-delete');
Route::get('productions/print/{productions}', [ProductionController::class, 'print'])->name('productions.print');
Route::put('productions/change-status/{production}', [ProductionController::class, 'changeStatus'])->name('productions.change-status');
Route::put('productions/change-stock-status/{production}', [ProductionController::class, 'changeStockStatus'])->name('productions.change-stock-status');
Route::put('productions/continue-production/{production}', [ProductionController::class, 'continueProduction'])->name('productions.continue-production');
Route::post('productions-{cpcs}-comment', [ProductionController::class, 'comment'])->name('productions.comment')->middleware('auth');
Route::get('productions-{cpcs}-show-traveler', [ProductionController::class, 'showTravelerTemplate'])->name('productions.show-traveler-template')->middleware('auth');
Route::post('productions-generate-box-label', [ProductionController::class, 'generateBoxLabel'])->name('productions.generate-box-label')->middleware('auth');
Route::post('productions-get-matches', [ProductionController::class, 'getMatches'])->name('productions.get-matches');
Route::get('productions-fetch-catalog-product-shipping-rates/{catalog_product}', [ProductionController::class, 'fetchCatalogProductShippingRates'])->name('productions.fetch-catalog-product-shipping-rates');
Route::get('productions-fetch-sale-info/{sale}', [ProductionController::class, 'fetchSaleInfo'])->name('productions.fetch-sale-info');

// ------- Quality department routes  ---------
Route::resource('qualities', QualityController::class)->middleware('auth');
Route::post('qualities/update-with-media/{quality}', [QualityController::class, 'updateWithMedia'])->name('qualities.update-with-media');
Route::get('qualities/get-production/{production_id}', [QualityController::class, 'getProduction'])->name('qualities.get-production')->middleware('auth');
Route::get('qualities/get-quality/{quality_id}', [QualityController::class, 'getQuality'])->name('qualities.get-quality')->middleware('auth');


// ------- Machines Routes  ---------
Route::resource('machines', MachineController::class)->middleware('auth');
Route::post('machines/massive-delete', [MachineController::class, 'massiveDelete'])->name('machines.massive-delete');
Route::post('machines/upload-files/{machine}', [MachineController::class, 'uploadFiles'])->name('machines.upload-files');
Route::post('machines/update-with-media/{machine}', [MachineController::class, 'updateWithMedia'])->name('machines.update-with-media')->middleware('auth');
Route::post('machines/QR-search-machine', [MachineController::class, 'QRSearchMachine'])->name('machines.QR-search-machine');

// ------- aditional time request Routes  ---------
Route::resource('more-additional-times', AdditionalTimeRequestController::class)->middleware('auth');
Route::post('more-additional-times/massive-delete', [AdditionalTimeRequestController::class, 'massiveDelete'])->name('more-additional-times.massive-delete');
Route::get('admin-additional-times', [AdditionalTimeRequestController::class, 'adminIndex'])->name('admin-additional-times.index');
Route::put('admin-additional-times/authorize/{admin_additional_time}', [AdditionalTimeRequestController::class, 'authorizeRequest'])->name('admin-additional-times.authorize');
Route::put('admin-additional-times/unauthorize/{admin_additional_time}', [AdditionalTimeRequestController::class, 'unauthorizeRequest'])->name('admin-additional-times.unauthorize');
Route::post('admin-additional-times/massive-delete', [AdditionalTimeRequestController::class, 'massiveDeleteAdmin'])->name('admin-additional-times.massive-delete');

// ------- PDF routes -------------------
Route::get('/raw-material-actual-stock', [PdfController::class, 'RawMaterialActualStock'])->name('pdf.raw-material-actual-stock')->middleware('auth');
Route::get('/consumables-actual-stock', [PdfController::class, 'consumablesActualStock'])->name('pdf.consumables-actual-stock')->middleware('auth');
Route::get('/raw-material-info', [PdfController::class, 'RawMaterialInfo'])->name('pdf.raw-material-info')->middleware('auth');


// ------- Maintenances routes  -------------
Route::resource('maintenances', MaintenanceController::class)->except('create')->middleware('auth');
Route::get('maintenances/create/{selectedMachine}', [MaintenanceController::class, 'create'])->name('maintenances.create')->middleware('auth');
Route::post('maintenances/update-with-media/{maintenance}', [MaintenanceController::class, 'updateWithMedia'])->name('maintenances.update-with-media')->middleware('auth');
Route::put('maintenances/validate/{maintenance}', [MaintenanceController::class, 'validateWork'])->name('maintenances.validate')->middleware('auth');


// ------- tutorials & manuals routes  -------------
Route::resource('manuals', ManualController::class)->middleware('auth');
Route::put('manuals/increase-views/{manual}', [ManualController::class, 'increaseViews'])->name('manuals.increase-views')->middleware('auth');
Route::post('manuals/update-with-media/{manual}', [ManualController::class, 'updateWithMedia'])->name('manuals.update-with-media')->middleware('auth');


// ---------- spare parts routes  ---------------
Route::resource('spare-parts', SparePartController::class)->except('create')->middleware('auth');
Route::get('spare-parts/create/{selectedMachine}', [SparePartController::class, 'create'])->name('spare-parts.create')->middleware('auth');
Route::post('spare-parts/update-with-media/{spare_part}', [SparePartController::class, 'updateWithMedia'])->name('spare-parts.update-with-media')->middleware('auth');

//------------------ Meetings routes ----------------
Route::resource('meetings', MeetingController::class)->middleware('auth');
Route::post('meetings/massive-delete', [MeetingController::class, 'massiveDelete'])->name('meetings.massive-delete');
Route::put('meetings/set-attendance-confirmation/{meeting_id}', [MeetingController::class, 'SetAttendanceConfirmation'])->name('meetings.set-attendance-confirmation');
Route::post('meetings/get-by-date-and-user', [MeetingController::class, 'getMeetingsByDateAndUser'])->name('meetings.get-by-date-and-user');


//------------------ production-cost routes ----------------
Route::resource('production-costs', ProductionCostController::class)->middleware('auth');
Route::post('production-costs/massive-delete', [ProductionCostController::class, 'massiveDelete'])->name('production-costs.massive-delete');


//------------------ Actions history routes ----------------
Route::resource('audits', AuditController::class)->middleware('auth');

//------------------ Settings routes ----------------
Route::resource('settings', SettingController::class)->middleware('auth');

//------------------ Production progress routes ----------------
Route::resource('production-progress', ProductionProgressController::class)->middleware('auth');

//------------------ Customer dates routes ----------------
Route::resource('customer-meetings', CustomerMeetingController::class)->middleware('auth');
Route::post('customer-meetings/get-soon-dates', [CustomerMeetingController::class, 'getSoonDates'])->name('customer-meetings.get-soon-dates')->middleware('auth');

//------------------ sale analisis routes ----------------
Route::resource('sale-analitics', SaleAnaliticController::class)->middleware('auth');
Route::get('sale-analitics-fetch-top-products/{family}/{range}/{type}', [SaleAnaliticController::class, 'fetchTopProducts'])->name('sale-analitics.fetch-top-products')->middleware('auth');
Route::get('sale-analitics-fetch-product-info/{part_number}', [SaleAnaliticController::class, 'fetchProductInfo'])->name('sale-analitics.fetch-product-info')->middleware('auth');
Route::get('sale-analitics-fetch-raw-material-info/{part_number}', [SaleAnaliticController::class, 'fetchRawMaterialInfo'])->name('sale-analitics.fetch-raw-material-info')->middleware('auth');
Route::get('sale-analitics-fetch-catalog-product-sales/{part_number}/{range}', [SaleAnaliticController::class, 'fetchCatalogProductSales'])->name('sale-analitics.fetch-catalog-product-sales')->middleware('auth');
Route::get('sale-analitics-get-estatistics-data/{date}', [SaleAnaliticController::class, 'getEstatisticsData'])->name('sale-analitics.get-estatistics-data')->middleware('auth');

//------------------ Kiosk routes ----------------
Route::post('kiosk', [KioskDeviceController::class, 'store'])->name('kiosk.store');

Route::post('/upload-image', [FileUploadController::class, 'upload'])->name('upload-image');


//contact routes ----------------------------------------------------------------
Route::put('contacts-update/{contact}', [ContactController::class, 'update'])->name('contacts.update')->middleware('auth');


//artisan commands -------------------
Route::get('/clear-all', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return 'cleared.';
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    return 'cleared.';
});

Route::get('/backup', function () {
    Artisan::call('app:backup-database');
    return 'backup created.';
});

// test mail
Route::get('mail-test', function () {
    $destinatario = 'maribel@emblemas3d.com';
    $mensaje = 'Este es un correo de prueba desde Laravel.';

    Mail::raw($mensaje, function ($message) use ($destinatario) {
        $message->to($destinatario)
            ->subject('Correo de prueba');
    });

    return "Correo de prueba enviado a $destinatario.";
});
