<?php

namespace App\Console\Commands;

use App\Models\CatalogProductCompany;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\ReactivateProductSaleNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReactivateProductSale extends Command
{
    protected $signature = 'app:reactivate-product-sale';
    protected $description = 'check if a product has more than x time whitout sale';

    public function handle()
    {
        // get days from settings
        $days = Setting::where('key', 'DAYS_TO_REACTIVATE_PRODUCT_SALE')->first()->value;
        $date_to_reactivate = Carbon::now()->subDays($days);

        // $products = DB::table('catalog_product_company')
        //     ->select('catalog_product_company.*')
        //     ->join('catalog_product_company_sale', 'catalog_product_company.id', '=', 'catalog_product_company_sale.catalog_product_company_id')
        //     ->join('sales', 'catalog_product_company_sale.sale_id', '=', 'sales.id')
        //     ->whereDate('sales.created_at', '<=', $date_to_reactivate)
        //     ->distinct()
        //     ->get();

        // productos de catalogo sin OV o DAYS_TO_REACTIVATE_PRODUCT_SALE dias si OV
        $products = DB::table('catalog_product_company')
            ->select('catalog_product_company.*')
            ->leftJoin('catalog_product_company_sale', 'catalog_product_company.id', '=', 'catalog_product_company_sale.catalog_product_company_id')
            ->leftJoin('sales', 'catalog_product_company_sale.sale_id', '=', 'sales.id')
            ->where(function ($query) use ($date_to_reactivate) {
                $query->whereDate('sales.created_at', '<=', $date_to_reactivate)
                    ->orWhereNull('sales.created_at');
            })
            ->distinct()
            ->get();

        if ($products->count()) {
            $super_admins = User::whereIn('id', [1, 2, 3])->get();
            $sellers = User::where('employee_properties->department', 'Ventas')->where('is_active', 1)->get();
            $direction = User::whereIn('employee_properties->puesto', ['Asistente de director'])->where('is_active', 1)->get();

            // notify users
            foreach ($sellers as $seller) {
                $seller->notify(new ReactivateProductSaleNotification($products, $days));
            }
            foreach ($direction as $item) {
                $item->notify(new ReactivateProductSaleNotification($products, $days));
            }
            foreach ($super_admins as $super) {
                $super->notify(new ReactivateProductSaleNotification($products, $days));
            }
            Log::info('app:reactivate-product-sale executed successfully. There where ' . $products->count() . ' product(s)');
        } else {
            Log::info('app:reactivate-product-sale executed successfully. There where 0 products');
        }
    }
}
