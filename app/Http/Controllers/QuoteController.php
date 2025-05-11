<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\QuoteResource;
use App\Models\Calendar;
use App\Models\CatalogProduct;
use App\Models\CatalogProductCompany;
use App\Models\CatalogProductCompanySale;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Oportunity;
use App\Models\Prospect;
use App\Models\Quote;
use App\Models\RawMaterial;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\ApprovalOkNotification;
use App\Notifications\ApprovalRequiredNotification;
use App\Notifications\NewQuoteNotification;
use App\Notifications\RequestApprovedNotification;
use App\Notifications\ScheduleUpdateProductPriceReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuoteController extends Controller
{
    public function index()
    {
        $pre_quotes = Quote::with(['catalogProducts:id,name', 'user:id,name', 'media'])
            ->latest()
            ->paginate(20); // Pagina primero

        $quotes = $pre_quotes->through(function ($quote) {
            return [
                'id' => $quote->id,
                'folio' => 'COT-' . str_pad($quote->id, 4, "0", STR_PAD_LEFT),
                'quote_acepted' => $quote->quote_acepted,
                'responded_at' => $quote->responded_at,
                'rejected_razon' => $quote->rejected_razon,
                'sale_id' => $quote->sale_id,
                'media' => $quote->media,
                'user' => [
                    'id' => $quote->user?->id,
                    'name' => $quote->user?->name
                ],
                'receiver' => $quote->receiver,
                'companyBranch' => [
                    'id' => $quote->companyBranch?->id,
                    'name' => $quote->companyBranch?->name
                ],
                'prospect' => [
                    'id' => $quote->prospect?->id,
                    'name' => $quote->prospect?->name
                ],
                'catalog_products' => $quote->catalogProducts->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name
                    ];
                }),
                'authorized_user_name' => $quote->authorized_user_name ?? '--',
                'authorized_at' => $quote->authorized_at,
                'created_by_customer' => $quote->created_by_customer,
                'created_at' => $quote->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
                'profit' => $quote->getProfit(), // Añadir el profit
            ];
        });

        // return $quotes;
        return inertia('Quote/Index', compact('quotes'));
    }

    public function create()
    {
        $catalog_products = CatalogProduct::with(['media'])->get(['id', 'name', 'part_number']);
        $raw_materials = RawMaterial::with(['media'])->get(['id', 'name', 'part_number']);
        $company_branches = CompanyBranch::get(['id', 'name']);
        $prospects = Prospect::get(['id', 'name', 'contact_name', 'contact_charge']);

        // si se accede a crear cotización desde oportunidad. Recupera la oportunidad y la manda para llenar el formulario.
        $opportunity = Oportunity::find(request('opportunityId'));

        return inertia('Quote/Create', compact('catalog_products', 'company_branches', 'opportunity', 'prospects', 'raw_materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver' => 'required|string|max:191',
            'department' => 'required|string|max:191',
            'tooling_cost' => 'required|min:0',
            'freight_cost' => 'required|string',
            'freight_option' => 'required|string',
            'first_production_days' => 'required|string|max:191',
            'notes' => 'nullable|string|max:800',
            'currency' => 'required|string|max:191',
            'company_branch_id' => 'nullable|numeric|min:1',
            'prospect_id' => 'nullable|numeric|min:1',
            'products' => 'array|min:1',
            'tooling_currency' => 'nullable',
            'show_breakdown' => 'boolean',
        ]);

        $quote = Quote::create($request->except('products') + ['user_id' => auth()->id()]);
        $can_authorize = auth()->user()->can('Autorizar cotizaciones') || auth()->user()->hasRole('Super admin');

        if ($can_authorize) {
            $quote->update(['authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        } else {
            // notify to Maribel
            $maribel = User::find(3);
            $maribel->notify(new ApprovalRequiredNotification('cotización', 'quotes.index'));
        }

        foreach ($request->products as $product) {
            if ($product['isCatalogProduct']) {
                $quoted_product = [
                    "quantity" => $product['quantity'],
                    "price" => $product['price'],
                    "show_image" => $product['show_image'],
                    "requires_med" => $product['requires_med'],
                    "notes" => $product['notes'],
                ];

                $quote->catalogProducts()->attach($product['id'], $quoted_product);
            } else {
                $quoted_product = [
                    "quantity" => $product['quantity'],
                    "price" => $product['price'],
                    "show_image" => $product['show_image'],
                    "notes" => $product['notes'],
                ];

                $quote->rawMaterials()->attach($product['id'], $quoted_product);
            }
        }

        event(new RecordCreated($quote));

        return to_route('quotes.index');
    }

    public function show(Quote $quote)
    {
        // Obtener todas las cotizaciones ordenadas por ID
        $quotes = Quote::orderBy('id')->get();

        // Encontrar la posición de la cotización actual en la lista
        $currentIndex = $quotes->search(function ($q) use ($quote) {
            return $q->id == $quote->id;
        });

        // Obtener el ID de la siguiente cotización, manejando el caso en el que estamos en la última cotización
        $nextQuote = $quotes->get(($currentIndex + 1) % $quotes->count());

        // Obtener el ID de la cotización anterior, manejando el caso en el que estamos en la primera cotización
        $prevQuote = $quotes->get(($currentIndex - 1 + $quotes->count()) % $quotes->count());

        // Preparar los recursos de la cotización actual
        $quote = QuoteResource::make(Quote::with(['catalogProducts', 'rawMaterials', 'prospect'])->findOrFail($quote->id));

        if ($quote->is_spanish_template) {
            return inertia('Quote/SpanishTemplate', [
                'quote' => $quote,
                'next_quote' => $nextQuote->id,
                'prev_quote' => $prevQuote->id,
            ]);
        } else {

            return inertia('Quote/EnglishTemplate', [
                'quote' => $quote,
                'next_quote' => $nextQuote->id,
                'prev_quote' => $prevQuote->id,
            ]);
        }
    }

    public function edit(Quote $quote)
    {
        $quote = $quote->load('catalogProducts', 'rawMaterials');
        $catalog_products = CatalogProduct::with(['media'])->get(['id', 'name', 'part_number']);
        $raw_materials = RawMaterial::with(['media'])->get(['id', 'name', 'part_number']);
        $company_branches = CompanyBranch::all(['id', 'name']);
        $prospects = Prospect::get(['id', 'name', 'contact_name', 'contact_charge']);

        return inertia('Quote/Edit', compact('catalog_products', 'raw_materials', 'company_branches', 'quote', 'prospects'));
    }

    public function update(Request $request, Quote $quote)
    {
        $request->validate([
            'receiver' => 'required|string|max:191',
            'department' => 'required|string|max:191',
            'tooling_cost' => 'required|min:0',
            'freight_cost' => 'required|string',
            'freight_option' => 'required|string',
            'first_production_days' => 'required|string|max:191',
            'notes' => 'nullable|string|max:800',
            'currency' => 'required|string|max:191',
            'company_branch_id' => 'nullable|numeric|min:1',
            'prospect_id' => 'nullable|numeric|min:1',
            'products' => 'array|min:1',
            'show_breakdown' => 'boolean',
        ]);

        $quote->update($request->except('products'));
        $quote->catalogProducts()->detach();
        $quote->rawMaterials()->detach();

        foreach ($request->products as $product) {

            $quoted_product = [
                "quantity" => $product['quantity'],
                "price" => $product['price'],
                "show_image" => $product['show_image'],
                "notes" => $product['notes'],
            ];

            if ($product['isCatalogProduct']) {
                $quote->catalogProducts()->attach($product['id'], $quoted_product);
            } else {
                $quote->rawMaterials()->attach($product['id'], $quoted_product);
            }
        }

        if ( !$quote->user_id ) {
            $quote->update([
                'user_id' => auth()->id()
            ]);
        }

        event(new RecordEdited($quote));

        return to_route('quotes.index');
    }

    public function destroy(Quote $quote)
    {
        //
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->quotes as $quote) {
            $quote = Quote::find($quote['id']);
            $quote?->delete();

            event(new RecordDeleted($quote));
        }

        return response()->json(['message' => 'Cotización(es) eliminada(s)']);
    }

    public function clone(Request $request)
    {
        $quote = Quote::find($request->quote_id);

        $clone = $quote->replicate()->fill([
            'authorized_user_name' => null,
            'authorized_at' => null,
            'user_id' => auth()->id(),
            'sale_id' => null,
        ]);

        $clone->save();

        $can_authorize = auth()->user()->can('Autorizar cotizaciones') || auth()->user()->hasRole('Super admin');

        if ($can_authorize) {
            $clone->update(['authorized_at' => now(), 'authorized_user_name' => auth()->user()->name]);
        } else {
            // notify to Maribel
            $maribel = User::find(3);
            $maribel->notify(new ApprovalRequiredNotification('cotización', 'quotes.index'));
        }

        foreach ($quote->catalogProducts as $product) {
            $company_id = $clone->companyBranch->company_id;
            $catalog_product_company = CatalogProductCompany::where('company_id', $company_id)
                ->where('catalog_product_id', $product->id)
                ->first();

            $pivot = [
                'quantity' => $product->pivot->quantity,
                // obtiene el precio mayor entre el precio de la cotización y el nuevo precio del producto catalogado
                'price' => $catalog_product_company ? max($product->pivot->price, $catalog_product_company->new_price) : $product->pivot->price,
                'notes' => $product->pivot->notes,
                'show_image' => $product->pivot->show_image,
            ];
            
            // revisar si new_date tiene mas de 1 año a la fecha actual. De ser asi, actualizamos new_price a +7.9%
             if ($catalog_product_company && $catalog_product_company->new_date->diffInDays(now()) >= 365) {
                $pivot['price'] = $product->pivot->price * 1.079;
                $catalog_product_company->update([
                    'oldest_date' => $catalog_product_company->old_date,
                    'oldest_price' => $catalog_product_company->old_price,
                    'oldest_currency' => $catalog_product_company->old_currency,
                    'old_date' => $catalog_product_company->new_date,
                    'old_price' => $catalog_product_company->new_price,
                    'old_currency' => $catalog_product_company->new_currency,
                    'oldest_updated_by' => $catalog_product_company->old_updated_by,
                    'old_updated_by' => $catalog_product_company->new_updated_by,                    
                    'new_price' => $pivot['price'],
                    'new_date' => today(),
                    'new_updated_by' => 'Automáticamente por el sistema',
                ]);
            }

            $clone->catalogProducts()->attach($product->pivot->catalog_product_id, $pivot);
        }
        $new_item_folio = 'COT-' . str_pad($clone->id, 4, "0", STR_PAD_LEFT);

        return response()->json(['message' => "Cotización clonada: $new_item_folio", 'newItem' => QuoteResource::make($clone)]);
    }

    public function createSO(Request $request)
    {
        $quote = Quote::find($request->quote_id);
        $folio = 'COT-' . str_pad($quote->id, 4, "0", STR_PAD_LEFT);
        $branch = $this->getOrCreateBranch($quote);
        $company = $branch->company;
        $can_authorize = auth()->user()->can('Autorizar ordenes de venta');

        // Crear la orden de venta
        $sale = Sale::create([
            'shipping_option' => "Entrega única",
            'freight_option' => $quote->freight_option,
            'freight_cost' => is_numeric($quote->freight_cost) ? $quote->freight_cost : 0,
            'freight_cost_charged_in_product' => $quote->freight_cost_charged_in_product,
            'order_via' => "Cotización folio $folio",
            'authorized_user_name' => $can_authorize ? auth()->user()->name : null,
            'authorized_at' => $can_authorize ? now() : null,
            'status' => $can_authorize ? 'Autorizado. Sin orden de producción' : 'Esperando autorización',
            'user_id' => auth()->id(),
            'notes' => $quote->notes,
            'company_branch_id' => $branch->id,
            'partialities' => [], // Inicialmente vacío
        ]);

        // relacionar venta con cotizacion
        $quote->update(['sale_id' => $sale->id]);

        $sale_folio = 'OV-' . str_pad($sale->id, 4, "0", STR_PAD_LEFT);

        // Inicializar parcialidades
        $partialities = [
            [
                'promise_date' => null,
                'shipping_cost' => null,
                'shipping_company' => null,
                'tracking_guide' => null,
                'sent_at' => null,
                'sent_by' => null,
                'number_of_packages' => null,
                'status' => 'Pendiente de envío',
                'productsSelected' => [] // Inicialmente vacío
            ]
        ];

        // Añadir productos a la venta
        foreach ($quote->catalogProducts as $product) {
            // Verificar si el producto ya está asociado con la compañía
            $catalog_product_company = $company->catalogProducts()
                ->where('catalog_product_id', $product->id)
                ->first(); // Obtener la relación si ya existe

            if (!$catalog_product_company) {
                // Si no existe, hacer attach con todos los campos requeridos
                $company->catalogProducts()->attach($product->id, [
                    'new_updated_by' => $quote->user->name,
                    'new_date' => today(),
                    'new_price' => $product->pivot->price,
                    'new_currency' => $quote->currency,
                    'user_id' => $quote->user_id,
                ]);

                // Obtener el registro que acabamos de asociar
                $catalog_product_company = $company->catalogProducts()
                    ->where('catalog_product_id', $product->id)
                    ->first();
            }

            // Crear la relación en la tabla pivot para la venta
            CatalogProductCompanySale::create([
                'catalog_product_company_id' => $catalog_product_company->pivot->id, // Usar el id del pivote
                'sale_id' => $sale->id,
                'quantity' => $product->pivot->quantity,
                'requires_medallion' => $product->pivot->requires_med,
                'notes' => $product->pivot->notes,
            ]);

            // Asignar producto a las parcialidades
            $partialities[0]['productsSelected'][] = [
                'id' => $product->id,
                'name' => $product->name,
                'selected' => true,
                'quantity' => $product->pivot->quantity,
            ];
        }

        foreach ($quote->rawMaterials as $rawMaterial) {
            $catalogProductExisting = $rawMaterial->isInCatalogProduct();
            // Verificar si la materia prima ya es un producto de catálogo
            if (!$catalogProductExisting) {
                // Si no está en el catálogo, convertirla
                $last = CatalogProduct::latest()->first();
                $next_id = $last ? $last->id + 1 : 1;
                $consecutive = str_pad($next_id, 4, "0", STR_PAD_LEFT);
                $family = explode('-', $rawMaterial->part_number)[0];
                $part_number = "C-$family-GEN-$consecutive";

                // Crear un nuevo producto de catálogo
                $catalogProduct = CatalogProduct::create([
                    'name' => $rawMaterial->name,
                    'description' => $rawMaterial->description,
                    'part_number' => $part_number,
                    'measure_unit' => $rawMaterial->measure_unit,
                    'cost' => $rawMaterial->cost,
                    'min_quantity' => $rawMaterial->min_quantity,
                    'max_quantity' => $rawMaterial->max_quantity,
                    'features' => $rawMaterial->features,
                ]);

                // Clonar la imagen si existe
                $rawMaterialImage = $rawMaterial->getFirstMedia();
                if ($rawMaterialImage && file_exists($rawMaterialImage->getPath())) {
                    $clonedImage = $catalogProduct
                        ->addMedia($rawMaterialImage->getPath())
                        ->preservingOriginal()
                        ->toMediaCollection();
                    $catalogProduct->media()->save($clonedImage);
                }

                // Agregar el material al producto de catálogo
                $catalogProduct->rawMaterials()->attach($rawMaterial, [
                    'quantity' => 1,
                    'production_costs' => [15],
                ]);
            } else {
                // Si ya está en el catálogo, obtener el producto asociado
                $catalogProduct = $catalogProductExisting;
            }

            // Verificar si el producto de catálogo ya está asociado con la compañía
            $exists = $company->catalogProducts()->where('catalog_product_id', $catalogProduct->id)->exists();

            if (!$exists) {
                // Si no existe, hacer attach con todos los campos requeridos
                $company->catalogProducts()->attach($catalogProduct->id, [
                    'new_updated_by' => $quote->user->name,
                    'new_date' => today(),
                    'new_price' => $rawMaterial->pivot->price,
                    'new_currency' => $quote->currency,
                    'user_id' => $quote->user_id,
                ]);

                // Obtener el registro que acabamos de asociar
                $catalog_product_company = $company->catalogProducts()
                    ->where('catalog_product_id', $catalogProduct->id)
                    ->first();
            } else {
                $catalog_product_company = $company->catalogProducts()
                    ->where('catalog_product_id', $catalogProduct->id)
                    ->first();
            }

            // Crear la relación en la tabla pivot para la venta
            CatalogProductCompanySale::create([
                'catalog_product_company_id' => $catalog_product_company->pivot->id,
                'sale_id' => $sale->id,
                'quantity' => $rawMaterial->pivot->quantity,
                'requires_medallion' => false,
                'notes' => $rawMaterial->pivot->notes,
            ]);

            // Asignar producto a las parcialidades
            $partialities[0]['productsSelected'][] = [
                'id' => $catalogProduct->id,
                'name' => $rawMaterial->name,
                'selected' => true,
                'quantity' => $rawMaterial->pivot->quantity,
            ];
        }

        // Guardar las parcialidades en la venta
        $sale->update(['partialities' => $partialities]);

        return response()->json([
            'message' => "Cotización convertida en orden de venta con folio: {$sale_folio}. Completar información de la misma",
            'sale_id' => $sale->id,
        ]);
    }

    /**
     * Obtiene o crea una sucursal a partir de la cotización.
     * Si la cotización proviene de un prospecto, lo convierte en cliente.
     */
    private function getOrCreateBranch($quote)
    {
        $folio = 'COT-' . str_pad($quote->id, 4, "0", STR_PAD_LEFT);
        if ($quote->company_branch_id) {
            return CompanyBranch::find($quote->company_branch_id);
        }

        // Convertir prospecto en cliente
        $prospect = $quote->prospect;
        $company = Company::create([
            'business_name' => $prospect->name,
            'phone' => $prospect->contact_phone,
            'rfc' => 'convertido desde prospecto con ID ' . $prospect->id,
            'post_code' => '12345',
            'fiscal_address' => $prospect->address ?? 'No especificado',
            'user_id' => $prospect->user_id,
            'seller_id' => $prospect->seller_id ?? auth()->id(),
            'branches_number' => $prospect->branches_number,
        ]);

        // Crear sucursal
        $branch = CompanyBranch::create([
            'name' => $company->business_name,
            'password' => bcrypt('e3d'),
            'address' => $company->fiscal_address ?? 'No especificado',
            'state' => $prospect->state,
            'post_code' => '12345',
            'meet_way' => 'Otro',
            'sat_method' => 'PUE',
            'sat_type' => 'G03',
            'sat_way' => '99',
            'company_id' => $company->id,
            'important_notes' => 'Cliente convertido de prospecto automáticamente al crear ' . $folio . ' a OV. Completar información faltante en el apartado de clientes y borrar esta nota.',
            'days_to_reactivate' => 30,
        ]);

        // Crear contacto para la sucursal
        $branch->contacts()->create([
            'name' => $prospect->contact_name,
            'email' => $prospect->contact_email,
            'phone' => $prospect->contact_phone,
            'charge' => $prospect->contact_charge,
        ]);

        // Actualizar cotizaciones del prospecto
        $prospect->quotes->each(function ($quote) use ($branch) {
            $quote->update(['company_branch_id' => $branch->id, 'prospect_id' => null]);
        });

        // Eliminar prospecto
        $prospect->delete();

        return $branch;
    }

    public function authorizeQuote(Quote $quote)
    {
        $quote->update([
            'authorized_at' => now(),
            'authorized_user_name' => auth()->user()->name,
        ]);

        // notificar a creador de la orden si quien autoriza no es el mismo usuario
        if (auth()->id() != $quote->user->id) {
            $quote_folio = 'COT-' . str_pad($quote->id, 4, "0", STR_PAD_LEFT);
            $quote->user->notify(new ApprovalOkNotification(
                'Cotización',
                $quote_folio,
                'quote',
                route('quotes.show', $quote->id)
            ));
        }

        return response()->json(['message' => 'Cotizacion autorizadda', 'item' => $quote]); //en caso de actualizar en la misma vista descomentar
        // return to_route('quotes.index'); // en caso de mandar al index, descomentar.
    }

    public function getMatches(Request $request)
    {
        $query = $request->input('query');

        // Realiza la búsqueda
        $pre_quotes = Quote::where('id', 'like', "%{$query}%")
            ->orWhereHas('user', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })->orWhereHas('companyBranch', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhere('receiver', 'like', "%{$query}%")
            ->with(['user:id,name', 'catalogProducts:id,name'])
            ->get();

        // Mapea los resultados al formato del index
        $quotes = $pre_quotes->map(function ($quote) {
            return [
                'id' => $quote->id,
                'folio' => 'COT-' . str_pad($quote->id, 4, "0", STR_PAD_LEFT),
                'user' => [
                    'id' => $quote->user->id,
                    'name' => $quote->user->name
                ],
                'receiver' => $quote->receiver,
                'companyBranch' => [
                    'id' => $quote->companyBranch?->id,
                    'name' => $quote->companyBranch?->name
                ],
                'prospect' => [
                    'id' => $quote->prospect?->id,
                    'name' => $quote->prospect?->name
                ],
                'catalog_products' => $quote->catalogProducts->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name
                    ];
                }),
                'authorized_user_name' => $quote->authorized_user_name ?? '--',
                'authorized_at' => $quote->authorized_at,
                'created_at' => $quote->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
                'profit' => $quote->getProfit(),
            ];
        });

        // Devuelve las cotizaciones encontradas
        return response()->json(['items' => $quotes], 200);
    }

    public function fetchCatalogProductsCompanyBranch($company_branch_id)
    {
        $company = Company::whereHas('companyBranches', function ($query) use ($company_branch_id) {
            $query->where('id', $company_branch_id);
        })
        ->with('catalogProducts', 'catalogProducts.media')
        ->first(['id', 'business_name']);

        $catalog_products_company = $company ? $company->catalogProducts : [];

        $catalog_products_company->load('rawMaterials.storages');
    
        return response()->json(['items' => $catalog_products_company, 'companyId' => $company->id]);
    }

    public function scheduleUpdateProductPrice(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'start_date' => 'required|date|after:yesterday',
            'description' => 'nullable|string|max:255',
        ]);

        $reminder = Calendar::create([
            'type' => 'Tarea',
            'title' => $request->title,
            'repeater' => 'No se repite',
            'description' => $request->description,
            'status' => 'Pendiente',
            'start_date' => $request->start_date,
            'start_time' => '8:00 AM',
            'user_id' => auth()->id(),
        ]);

        $user = auth()->user();
        $user->notify(new ScheduleUpdateProductPriceReminder($reminder));

        // actualizar bandera de aviso invasivo de recordatorio ------------------------------
        $today = now()->toDateString();
        $dateOneDayAhead = now()->addDays(1)->toDateString();

        // Obtener recordatorios entre hoy y 1 día adelante o pasados con estado "Pendiente"
        $reminders = Calendar::where(function ($query) use ($today, $dateOneDayAhead) {
                $query->whereBetween('start_date', [$today, $dateOneDayAhead])
                    ->orWhere('start_date', '<', $today);
            })
            ->where('title', 'like', 'Cambiar precio%')
            ->where('status', 'Pendiente')
            ->get();

        // Variable para rastrear si se encontró al menos un recordatorio importante
        $hasImportantReminder = $reminders->isNotEmpty();

        // Actualizar la propiedad `has_important_reminder` en la tabla `users`
        User::query()->update(['has_important_reminder' => $hasImportantReminder]);
    }

    public function fetchQuoteData(Quote $quote) 
    {
        $quote->load([
            'companyBranch', // Carga la relación company dentro de companyBranch
            'catalogProducts'
        ]);

        return response()->json(['quote' => $quote]);
    }

}
