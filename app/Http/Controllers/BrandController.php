<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CatalogProduct;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Termwind\Components\Raw;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::query()
            ->select('id', 'name', 'is_luxury')
            ->orderBy('name')
            ->get();

        return inertia('Brand/Index', compact('brands'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'is_luxury' => 'boolean',
        ]);

        Brand::create($request->all());
    }

    public function show(Brand $brand)
    {
        //
    }

    public function edit(Brand $brand)
    {
        return inertia('Brand/Edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $old_name = $brand->name;

        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'is_luxury' => 'boolean',
        ]);

        $brand->update($request->all());

        // actualizar rawmaterials y catalogProducts que tenian el nombre antiguo
        RawMaterial::where('brand', $old_name)
            ->update(['brand' => $request->name]);
        CatalogProduct::where('brand', $old_name)
            ->update(['brand' => $request->name]);

        return to_route('brands.index');
    }

    public function destroy(Brand $brand, Request $request)
    {
        // actualizar con la nueva marca rawmaterials y catalogProducts que tenian el nombre antiguo
        RawMaterial::where('brand', $brand->name)
            ->update(['brand' => $request->brand]);
        CatalogProduct::where('brand', $brand->name)
            ->update(['brand' => $request->brand]);

        $brand->delete();
    }
}
