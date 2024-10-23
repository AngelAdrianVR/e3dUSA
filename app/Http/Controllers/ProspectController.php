<?php

namespace App\Http\Controllers;

use App\Events\RecordDeleted;
use App\Http\Resources\ProspectResource;
use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Prospect;
use App\Models\User;
use Illuminate\Http\Request;

class ProspectController extends Controller
{

    public function index()
    {
        $prospects = Prospect::with('user:id,name', 'seller:id,name')
            ->paginate(30, ['id', 'name', 'created_at', 'contact_name', 'contact_phone', 'status', 'user_id', 'seller_id']);

        return inertia('Prospect/Index', compact('prospects'));
    }

    public function create()
    {
        $sellers = User::where('employee_properties->department', 'Ventas')->get(['id', 'name', 'profile_photo_path']);

        return inertia('Prospect/Create', compact('sellers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|string|max:255',
            'contact_charge' => 'required|string|max:255',
            'contact_phone' => 'required|string',
            'contact_phone_extension' => 'nullable|string|max:5',
            'contact_whatsapp' => 'nullable|string',
            'status' => 'required|string|max:255',
            'branches_number' => 'nullable|numeric|min:1',
            'abstract' => 'required|string|max:800',
            'seller_id' => 'nullable|numeric|min:1',
        ]);

        $prospect = Prospect::create($validated + ['user_id' => auth()->id(), 'seller_id' => auth()->id()]);

        if ( !$request->quick_creation ) {
            return to_route('prospects.show', $prospect);
        }
    }

    public function show(Prospect $prospect)
    {
        $prospect = ProspectResource::make($prospect->load(['seller', 'user']));
        $prospects = Prospect::latest()->get(['id', 'name']);

        $defaultTab = request('defaultTab');

        return inertia('Prospect/Show', compact('prospect', 'prospects', 'defaultTab'));
    }

    public function edit(Prospect $prospect)
    {
        $prospect = $prospect->load('seller');
        $sellers = User::where('employee_properties->department', 'Ventas')->get(['id', 'name', 'profile_photo_path']);

        return inertia('Prospect/Edit', compact('prospect', 'sellers'));
    }

    public function update(Request $request, Prospect $prospect)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|string|max:255',
            'contact_charge' => 'required|string|max:255',
            'contact_phone' => 'required|string',
            'contact_phone_extension' => 'nullable|string|max:5',
            'contact_whatsapp' => 'nullable|string',
            'status' => 'required|string|max:255',
            'branches_number' => 'nullable|numeric|min:1',
            'abstract' => 'required|string|max:800',
            'seller_id' => 'nullable|numeric|min:1',
        ]);

        $prospect->update($validated);

        return to_route('prospects.show', $prospect);
    }

    public function destroy(Prospect $prospect)
    {
        // Eliminar cotizaciones relacionadas
        $prospect->quotes()->delete(); 

        // eliminar prospecto
        $prospect->delete();
        event(new RecordDeleted($prospect));

        return to_route('prospects.index');
    }

    public function getMatches($query)
    {
        if ($query != 'nullable') {
            $prospects = ProspectResource::collection(Prospect::with('seller')
                ->where('name', 'LIKE', "%$query%")
                ->orWhere('contact_name', 'LIKE', "%$query%")
                ->orWhere('contact_phone', 'LIKE', "%$query%")
                ->orWhereHas('seller', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%$query%");
                })
                ->latest()
                ->get());
        } else {
            $prospects = ProspectResource::collection(Prospect::with('seller')->latest()->paginate(20));
        }

        return response()->json(['items' => $prospects]);
    }

    public function getQuotes(Prospect $prospect)
    {
        $quotes = $prospect->quotes->load('user');

        return response()->json(['items' => $quotes]);
    }

    public function turnIntoCustomer(Prospect $prospect)
    {
        // crear nuevo cliente
        $customer_data = [
            'business_name' => $prospect->name,
            'phone' => $prospect->contact_phone,
            'rfc' => 'RFC No especificado de prospecto ' . $prospect->id,
            'post_code' => '12345',
            'fiscal_address' => $prospect->address ?? 'No especificado',
            'branches_number' => $prospect->branches_number,
            'seller_id' => $prospect->seller_id ?? auth()->id(),
        ];
        $company = Company::create($customer_data + ['user_id' => auth()->id()]);

        // crear sucursal
        $branch = [
            'company_id' => $company->id,
            'name' => $prospect->name,
            'password' => bcrypt('e3d'),
            'address' => $prospect->address ?? 'No especificado',
            'state' => $prospect->state,
            'post_code' => '12345',
            'meet_way' => 'Convertido desde prospecto',
            'sat_method' => 'PDD',
            'sat_type' => 'G03',
            'sat_way' => 'Transferencia electrónica de fondos',
            'days_to_reactivate' => 30,
            'important_notes' => "Este cliente fue creado desde un prospecto por " . auth()->user()->name . " el " . now()->isoFormat('DD MMM, YYYY h:mm A') . ". Algunos datos fueron prellenados por el sistema automáticamente, revisar que la información sea correcta. Resumen de prospecto: $prospect->abstract",
        ];
        $compay_branch = CompanyBranch::create($branch);

        // crear contacto
        $contact = [
            'name' => $prospect->contact_name,
            'email' => $prospect->contact_email,
            'phone' => $prospect->contact_phone,
            'charge' => $prospect->contact_charge,
        ];
        $compay_branch->contacts()->create($contact);

        // pasar sus contizaciones a "cliente"
        $prospect->quotes->each(function ($quote) use ($compay_branch) {
            $quote->update(['company_branch_id' => $compay_branch->id, 'prospect_id' => null]);
        });

        // eliminar prospecto
        $prospect->delete();

        return response()->json(['company_id' => $company->id]);
    }
}
