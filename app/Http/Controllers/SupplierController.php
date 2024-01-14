<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordDeleted;
use App\Events\RecordEdited;
use App\Http\Resources\SupplierResource;
use App\Models\Contact;
use App\Models\RawMaterial;
use App\Models\Supplier;
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

                // return $suppliers;

        return inertia('Supplier/Index', compact('suppliers'));
    }

    
    public function create()
    {
        $raw_materials = RawMaterial::get(['id', 'name', 'cost']);

        // return $raw_materials;

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
        foreach ($request->rawMaterials as $raw_material ) {
            $raw_material_db = RawMaterial::find($raw_material['id']);
            $raw_material_db->cost = $raw_material['cost']; //actualiza el costo de la materia prima
            $raw_material_db->min_quantity_purchase = $raw_material['min_quantity_purchase']; //agrega el atributo al registro de raw_material
            $raw_material_db->notes = $raw_material['notes']; //agrega el atributo al registro de raw_material
            $raw_material_db->save();
            $raw_materials_id[] = $raw_material['id'];
       }

       $supplier = Supplier::create($request->except(['contacts', 'rawMaterials']) + ['raw_materials_id' => $raw_materials_id]);

       foreach ($request->contacts as $contact ) {
            $supplier->contacts()->create($contact);
       }

       event(new RecordCreated($supplier));

        return to_route('suppliers.index');
    }

    
    public function show($supplier_id)
    {
        $supplier = SupplierResource::make(Supplier::with('contacts')->find($supplier_id));
        $pre_suppliers = Supplier::latest()->get();
        $suppliers = $pre_suppliers->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
                   ];
               });

        // return $supplier;

        return inertia('Supplier/Show', compact('supplier', 'suppliers'));
    }

    
    public function edit($supplier_id)
    {
        $supplier = Supplier::with('contacts')->find($supplier_id);
        $raw_materials = RawMaterial::get(['id', 'name', 'cost']);

        // return $supplier;
        return inertia('Supplier/Edit', compact('supplier','raw_materials'));
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
        foreach ($request->rawMaterials as $raw_material ) {
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
                'birthdate_day' => $contactData['birthdate_day'],
                'birthdate_month' => $contactData['birthdate_month'],
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

}
