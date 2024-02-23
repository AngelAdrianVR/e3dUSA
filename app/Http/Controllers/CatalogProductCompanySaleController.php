<?php

namespace App\Http\Controllers;

use App\Models\CatalogProductCompanySale;
use Illuminate\Http\Request;

class CatalogProductCompanySaleController extends Controller
{
    public function storeTravelerData(CatalogProductCompanySale $cpcs, Request $request)
    {
        $cpcs->update([
            'traveler_data' => $request->traveler_data
        ]);

        return response()->json([]);
    }

    public function getTravelerData(CatalogProductCompanySale $cpcs)
    {
        return response()->json(['item' => $cpcs->traveler_data]);
    }

    public function getProductions(CatalogProductCompanySale $cpcs)
    {
        return response()->json(['items' => $cpcs->productions]);
    }

    public function getRawMaterials(CatalogProductCompanySale $cpcs)
    {
        $raw_materials =  $cpcs->catalogProductCompany->catalogProduct->rawMaterials;

        return response()->json(['items' => $raw_materials]);
    }
}
