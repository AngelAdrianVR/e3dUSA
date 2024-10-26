<?php

namespace App\Http\Controllers;

use App\Models\CatalogProductCompanySale;
use App\Models\User;
use App\Notifications\TravelerCriteriaRejectedNotification;
use Illuminate\Http\Request;

class CatalogProductCompanySaleController extends Controller
{
    public function storeTravelerData(CatalogProductCompanySale $cpcs, Request $request)
    {
        $cpcs->update([
            'traveler_data' => $request->traveler_data
        ]);

        // notificar a usuarios de rechazo en criterios de aceptación
        $users = User::whereNull('employee_properties')->orWhereIn('id', [4, 37])->get();
        $user_name = auth()->user()->name;
        $traveler_folio = 'HV-' . str_pad($cpcs->id, 4, "0", STR_PAD_LEFT);
        $production_folio = 'OP-' . str_pad($cpcs->sale_id, 4, "0", STR_PAD_LEFT);
        $users->each(fn ($user) => $user->notify(new TravelerCriteriaRejectedNotification($user_name, $traveler_folio, $production_folio)));

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

    public function getEstimatedCompletionDate(CatalogProductCompanySale $cpcs)
    {
        $item =  $cpcs->getEstimatedCompletionDate();

        return response()->json(['item' => $item]);
    }
}
