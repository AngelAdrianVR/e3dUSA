<?php

namespace App\Http\Controllers;

use App\Models\CatalogProductCompanySale;

class SaleAnaliticController extends Controller
{
    
    public function index()
    {
        $top_key_rings = $this->getKeyRingsSalesTop();
        $top_emblems = $this->getEmblemsSalesTop();
        $top_plates = $this->getPlatesSalesTop();
        $top_pdocuments = $this->getPdocumentsSalesTop();
        $top_develation = $this->getDevelationSalesTop();
        $top_pasarol = $this->getParasolSalesTop();
        $top_key_ring_case = $this->getKeyRingCaseSalesTop();
        $top_key_ring_case_aluminum = $this->getKeyRingCaseAluminumSalesTop();
        $top_mats = $this->getMatsSalesTop();
        $top_estiren = $this->getEstirenSalesTop();

        // return $top_estiren;

        return inertia('SaleAnalitic/Index');
    }


    public function getTopSalesByType($productType)
    {
        $product = CatalogProductCompanySale::with(['catalogProductCompany' => ['catalogProduct', 'company']])
            ->whereHas('catalogProductCompany.catalogProduct', function ($query) use ($productType) {
                $query->where('part_number', 'like', $productType . '%');
            })
            ->get();

        $agrouped = $product->groupBy('catalogProductCompany.catalogProduct.part_number')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->catalogProductCompany->catalogProduct->name,
                    'part_number' => $group->first()->catalogProductCompany->catalogProduct->part_number,
                    'min_stock' => $group->first()->catalogProductCompany->catalogProduct->min_quantity,
                    'old_price' => $group->first()->catalogProductCompany->old_price,
                    'new_price' => $group->first()->catalogProductCompany->new_price,
                    'quantity_sales' => $group->sum('quantity'),
                    'total_money' => $group->sum('quantity') * $group->first()->catalogProductCompany->new_price,
                    'client' => $group->first()->catalogProductCompany->company->business_name,
                ];
            })
            ->sortByDesc('quantity_sales')
            ->take(20); // Limitar a 20 elementos

        return $agrouped;
    }

    public function getKeyRingsSalesTop()
    {
        return $this->getTopSalesByType('C-LL');
    }

    public function getEmblemsSalesTop()
    {
        return $this->getTopSalesByType('C-EM');
    }

    public function getPlatesSalesTop()
    {
        return $this->getTopSalesByType('C-PP');
    }

    public function getPdocumentsSalesTop()
    {
        return $this->getTopSalesByType('C-PP');
    }

    public function getDevelationSalesTop()
    {
        return $this->getTopSalesByType('C-MT');
    }

    public function getParasolSalesTop()
    {
        return $this->getTopSalesByType('C-PS');
    }

    public function getKeyRingCaseSalesTop()
    {
        return $this->getTopSalesByType('C-FLL');
    }

    public function getKeyRingCaseAluminumSalesTop()
    {
        return $this->getTopSalesByType('C-FA');
    }

    public function getMatsSalesTop()
    {
        return $this->getTopSalesByType('C-TP');
    }

    public function getEstirenSalesTop()
    {
        return $this->getTopSalesByType('C-PE');
    }


    

}
