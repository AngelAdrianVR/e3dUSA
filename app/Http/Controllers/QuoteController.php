<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\QuoteResource;
use App\Models\CatalogProduct;
use App\Models\CatalogProductCompany;
use App\Models\CatalogProductCompanySale;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Oportunity;
use App\Models\Prospect;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\ApprovalRequiredNotification;
use App\Notifications\NewQuoteNotification;
use App\Notifications\RequestApprovedNotification;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        //Optimizacion para rapidez. No carga todos los datos, sólo los siguientes para hacer la busqueda y mostrar la tabla en index
        $pre_quotes = Quote::latest()->get();
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
                'authorized_user_name' => $quote->authorized_user_name ?? '--',
                'authorized_at' => $quote->authorized_at,
                'created_at' => $quote->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
                'profit' => $quote->getProfit(),
            ];
        });

        return inertia('Quote/Index', compact('quotes'));
    }

    public function create()
    {
        $catalog_products = CatalogProduct::all();
        $company_branches = CompanyBranch::get(['id', 'name']);
        $prospects = Prospect::get(['id', 'name', 'contact_name', 'contact_charge']);

        // si se accede a crear cotización desde oportunidad. Recupera la oportunidad y la manda para llenar el formulario.
        $opportunity = Oportunity::find(request('opportunityId'));

        // return $opportunity;

        return inertia('Quote/Create', compact('catalog_products', 'company_branches', 'opportunity', 'prospects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver' => 'required|string|max:191',
            'department' => 'required|string|max:191',
            'tooling_cost' => 'required|min:0',
            'freight_cost' => 'required|string',
            'first_production_days' => 'required|string|max:191',
            'notes' => 'nullable|string|max:191',
            'currency' => 'required|string|max:191',
            'company_branch_id' => 'nullable|numeric|min:1',
            'prospect_id' => 'nullable|numeric|min:1',
            'products' => 'array|min:1',
            'tooling_currency' => 'nullable'
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
            $quoted_product = [
                "quantity" => $product['quantity'],
                "price" => $product['price'],
                "show_image" => $product['show_image'],
                "notes" => $product['notes'],
            ];
            $quote->catalogProducts()->attach($product['id'], $quoted_product);
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
        $quote = QuoteResource::make(Quote::with(['catalogProducts', 'prospect'])->findOrFail($quote->id));

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
        $quote = $quote->load('catalogProducts');
        $catalog_products = CatalogProduct::all();
        $company_branches = CompanyBranch::all(['id', 'name']);
        $prospects = Prospect::get(['id', 'name', 'contact_name', 'contact_charge']);

        return inertia('Quote/Edit', compact('catalog_products', 'company_branches', 'quote', 'prospects'));
    }

    public function update(Request $request, Quote $quote)
    {
        $request->validate([
            'receiver' => 'required|string|max:191',
            'department' => 'required|string|max:191',
            'tooling_cost' => 'required|min:0',
            'freight_cost' => 'required|string',
            'first_production_days' => 'required|string|max:191',
            'notes' => 'nullable|string|max:191',
            'currency' => 'required|string|max:191',
            'company_branch_id' => 'nullable|numeric|min:1',
            'prospect_id' => 'nullable|numeric|min:1',
            'products' => 'array|min:1'
        ]);

        $quote->update($request->except('products'));
        $quote->catalogProducts()->detach();

        foreach ($request->products as $product) {
            $quote->catalogProducts()->attach($product['catalog_product_id'], $product);
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
            $pivot = [
                'quantity' => $product->pivot->quantity,
                'price' => $product->pivot->price,
                'notes' => $product->pivot->notes,
                'show_image' => $product->pivot->show_image,
            ];

            $clone->catalogProducts()->attach($product->pivot->catalog_product_id, $pivot);
        }
        $new_item_folio = 'COT-' . str_pad($clone->id, 4, "0", STR_PAD_LEFT);

        return response()->json(['message' => "Cotización clonada: $new_item_folio", 'newItem' => QuoteResource::make($clone)]);
    }

    public function createSO(Request $request)
    {
        $quote = Quote::find($request->quote_id);
        $folio = 'COT-' . str_pad($quote->id, 4, "0", STR_PAD_LEFT);
        $branch = CompanyBranch::find($quote->company_branch_id);

        $sale = Sale::create([
            'freight_cost' => $quote->freight_cost,
            'order_via' => "Cotización folio $folio",
            'authorized_user_name' => auth()->user()->can('Autorizar ordenes de venta') || auth()->user()->hasRole('Super admin') ? auth()->user()->name : null,
            'authorized_at' => auth()->user()->can('Autorizar ordenes de venta') || auth()->user()->hasRole('Super admin') ? now() : null,
            'user_id' => auth()->id(),
            'company_branch_id' => $quote->company_branch_id,
        ]);

        $sale_folio = 'OV-' . str_pad($sale->id, 4, "0", STR_PAD_LEFT);

        // add products for sale to sale
        foreach ($quote->catalogProducts as $product) {
            $catalog_product_company = $branch->company->catalogProducts->first(fn ($item) => $item->id == $product->id);
            if (!$catalog_product_company) {
                // register products to company if any required
                $pivot = [
                    'new_date' => today(),
                    'new_price' => $product->pivot->price,
                    'new_currency' => $quote->currency,
                ];
                $branch->company->catalogProducts()->attach($product->pivot->catalog_product_id, $pivot);
                $branch = CompanyBranch::find($quote->company_branch_id);
                $catalog_product_company = $branch->company->catalogProducts->last();
            }

            CatalogProductCompanySale::create([
                'catalog_product_company_id' => $catalog_product_company->pivot->id,
                'sale_id' => $sale->id,
                'quantity' => $product->pivot->quantity,
                'notes' => $product->pivot->notes,
            ]);
        }

        return response()->json(['message' => "Cotización convertida en orden de venta con folio: {$sale_folio}"]);
    }

    public function authorizeQuote(Quote $quote)
    {
        $quote->update([
            'authorized_at' => now(),
            'authorized_user_name' => auth()->user()->name,
        ]);

        // notify to requester user
        $quote_folio = 'COT-' . str_pad($quote->id, 4, "0", STR_PAD_LEFT);
        $quote->user->notify(new RequestApprovedNotification('Cotización', $quote_folio, "Cliente {$quote->companyBranch->name}", 'quote'));

        return response()->json(['message' => 'Cotizacion autorizadda', 'item' => $quote]); //en caso de actualizar en la misma vista descomentar
        // return to_route('quotes.index'); // en caso de mandar al index, descomentar.
    }
}
