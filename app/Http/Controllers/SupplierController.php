<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\SupplierResource;
use App\Models\Contact;
use App\Models\Purchase;
use App\Models\RawMaterial;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        // $suppliers = SupplierResource::collection(Supplier::latest()->get());
        //Optimizacion para rapidez. No carga todos los datos, sólo los siguientes para hacer la busqueda y mostrar la tabla en index
        $pre_suppliers = SupplierResource::collection(Supplier::latest()->get());
        $suppliers = $pre_suppliers->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'nickname' => $supplier->nickname,
                'phone' => $supplier->phone,
                'address' => $supplier->address,
                'post_code' => $supplier->post_code,
                'created_at' => $supplier->created_at?->isoFormat('DD MMMM YYYY, h:mm A'),
            ];
        });

        if (auth()->user()->can('Ver info sensible de proveedores')) {
            return inertia('Supplier/Index', compact('suppliers'));
        } else {
            return inertia('Supplier/IndexLimited', compact('suppliers'));
        }
    }

    public function create()
    {
        $raw_materials = RawMaterial::get(['id', 'name', 'cost']);

        return inertia('Supplier/Create', compact('raw_materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'post_code' => 'nullable|string|min:4|max:9',
            'phone' => 'required|string|min:10|max:13',
            'banks' => 'array|min:1',
            'contacts' => 'array|min:1',
            'rawMaterials' => 'array|min:0'
        ]);

        $raw_materials_id = [];

        //Guarda el costo de cada materia prima en la base de datos y guarda en un array los puros id de las materias primas
        //para despues guardarla en el json de la bd
        foreach ($request->rawMaterials as $raw_material) {
            $raw_material_db = RawMaterial::find($raw_material['id']);
            $raw_material_db->cost = $raw_material['cost']; //actualiza el costo de la materia prima
            $raw_material_db->min_quantity_purchase = $raw_material['min_quantity_purchase']; //agrega el atributo al registro de raw_material
            $raw_material_db->notes = $raw_material['notes']; //agrega el atributo al registro de raw_material
            $raw_material_db->save();
            $raw_materials_id[] = $raw_material['id'];
        }

        $supplier = Supplier::create($request->except(['contacts', 'rawMaterials']) + ['raw_materials_id' => $raw_materials_id]);

        foreach ($request->contacts as $contact) {
            $supplier->contacts()->create($contact);
        }

        event(new RecordCreated($supplier));

        return to_route('suppliers.index');
    }

    public function show($supplier_id)
    {
        $supplier = SupplierResource::make(Supplier::with('contacts')->find($supplier_id));
        $suppliers = Supplier::latest()->get(['id', 'name', 'nickname']);

        if (auth()->user()->can('Ver info sensible de proveedores')) {
            return inertia('Supplier/Show', compact('supplier', 'suppliers'));
        } else {
            return inertia('Supplier/ShowLimited', compact('supplier', 'suppliers'));
        }
    }

    public function edit($supplier_id)
    {
        $supplier = Supplier::with('contacts')->find($supplier_id);
        $raw_materials = RawMaterial::get(['id', 'name', 'cost']);

        return inertia('Supplier/Edit', compact('supplier', 'raw_materials'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'post_code' => 'nullable|string|min:4|max:9',
            'phone' => 'required|string|min:10|max:13',
            'banks' => 'array|min:1',
            'rawMaterials' => 'array|min:0'
        ]);

        $raw_materials_id = [];

        //Guarda el costo de cada materia prima en la base de datos y guarda en un array los puros id de las materias primas
        //para despues guardarla en el json de la bd
        foreach ($request->rawMaterials as $raw_material) {
            $raw_material_db = RawMaterial::find($raw_material['id']);
            $raw_material_db->cost = $raw_material['cost']; //actualiza el costo de la materia prima
            $raw_material_db->min_quantity_purchase = $raw_material['min_quantity_purchase']; //agrega el atributo al registro de raw_material
            $raw_material_db->notes = $raw_material['notes']; //agrega el atributo al registro de raw_material
            $raw_material_db->save();
            $raw_materials_id[] = $raw_material['id'];
        }

        $supplier->update($request->except(['contacts', 'rawMaterials']) + ['raw_materials_id' => $raw_materials_id]);

        //Para actualizar los contactos editados del supplier----------

        //Obtén los IDs de los contactos proporcionadoe en el formulario
        $existingContactIds = collect($request->input('contacts'))->pluck('id')->toArray();
        //Recorre los contactos proporcionados en el formulario
        foreach ($request->input('contacts') as $contactData) {
            $contact = null;

            //Comprueba si el co ntacto ya existe por ID
            if (isset($contactData['id'])) {
                $contact = Contact::find($contactData['id']);
            }

            //Si el contacto no existe se crea uno nuevo
            if (!$contact) {
                $contact = new Contact([
                    'user_id' => auth()->id(),
                ]);
            }

            //Actualiza los datos del contacto
            $contact->fill([
                'name' => $contactData['name'],
                'email' => $contactData['email'],
                'phone' => $contactData['phone'],
            ]);

            //Guarda el contacto en la relación
            $supplier->contacts()->save($contact);
        }

        //Elimina los contactos que no están en el formulario
        $supplier->contacts()->whereNotIn('id', $existingContactIds)->delete();

        event(new RecordEdited($supplier));

        return to_route('suppliers.index');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier_name = $supplier->name;
        $supplier->delete();

        event(new RecordDeleted($supplier));

        return response()->json(['message' => "Producto eliminado: $supplier_name"]);
    }

    public function massiveDelete(Request $request)
    {
        foreach ($request->suppliers as $supplier) {
            $supplier = Supplier::find($supplier['id']);
            $supplier?->delete();

            event(new RecordDeleted($supplier));
        }

        return response()->json(['message' => 'proveedor(es) eliminado(s)']);
    }

    public function fetchSupplier($supplier_id)
    {
        $supplier = Supplier::with('contacts')->find($supplier_id);

        return response()->json(['item' => $supplier]);
    }

    public function getOrders(Supplier $supplier)
    {
        $orders = $supplier->orders->load(['user']);

        return response()->json(['items' => $orders]);
    }

    public function ratingReport($p)
    {
        $suppliers = request('s'); //obtiene id de proveedres. Ej. [1,5,9,12,32]
        $year = explode('-', $p)[0];
        $month = explode('-', $p)[1];

        // Obtener las órdenes con ratings, junto con la información del proveedor
        $orders = Purchase::with(['supplier'])
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNotNull('rating')
            ->whereIn('supplier_id', $suppliers)
            ->get();

        // Inicializamos un array para agrupar los resultados por proveedor
        $groupedResults = [];

        // Iteramos sobre las órdenes para agruparlas por proveedor
        foreach ($orders as $order) {
            $supplierId = $order->supplier->id;
            $supplierName = $order->supplier->name;
            $supplierNickname = $order->supplier->nickname;

            // Inicializamos el proveedor en el array si aún no existe
            if (!isset($groupedResults[$supplierId])) {
                $groupedResults[$supplierId] = [
                    'supplier_name' => $supplierName,
                    'supplier_nickname' => $supplierNickname,
                    'supplier_id' => $supplierId,
                    'total_purchases' => 0,
                    'total_points' => 0,
                    'ratings_count' => 0
                ];
            }

            // Incrementamos el contador de compras para este proveedor
            $groupedResults[$supplierId]['total_purchases']++;

            // Sumamos los puntos de la evaluación (rating)
            foreach ($order->rating['questions'] as $question) {
                $groupedResults[$supplierId]['total_points'] += $question['points'];
            }

            // Incrementamos el contador de evaluaciones
            $groupedResults[$supplierId]['ratings_count']++;
        }

        // Convertimos los puntos totales a un promedio
        foreach ($groupedResults as &$supplier) {
            if ($supplier['ratings_count'] > 0) {
                $supplier['avg_points'] = number_format($supplier['total_points'] / $supplier['ratings_count'], 2);
            } else {
                $supplier['avg_points'] = 0;
            }

            // Limpiamos los datos innecesarios
            unset($supplier['total_points'], $supplier['ratings_count']);
        }

        return inertia('Supplier/RatingReport', [
            'data' => $groupedResults,
            'period' => Carbon::parse($p)->isoFormat('MMMM YYYY'),
        ]);
    }

    public function clone(Request $request)
    {
        $supplier = Supplier::find($request->supplier_id);

        $clone = $supplier->replicate()->fill([
            'name' => $supplier->name . ' (Clonado)',
            'nickname' => $supplier->nickname . ' (Clonado)',
            'part_number' => $supplier->part_number . '-Clonado',
            'user_id' => auth()->id(),
            'sale_id' => null,
        ]);

        $clone->save();
        
        // Clonar contactos y relacionarlos con el proveedor clonado
        foreach ($supplier->contacts as $contact) {
            $clone_contact = $contact->replicate()->fill([
                'contactable_id' => $clone->id,
                'contactable_type' => Supplier::class,
            ]);

            $clone_contact->save();
        }

        return response()->json(['message' => "Proveedor clonado: {$clone->nickname}", 'newItem' => $clone->load('contacts')]);
    }
}
