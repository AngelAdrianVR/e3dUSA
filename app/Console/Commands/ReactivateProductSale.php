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

class ReactivateProductSale extends Command
{
    protected $signature = 'app:reactivate-product-sale';
    protected $description = 'check if a product has more than x time whitout sale';

    public function handle()
    {
        // get days from settings
        $days = Setting::where('key', 'DAYS_TO_REACTIVATE_PRODUCT_SALE')->first()->value;

        $date_to_reactivate = Carbon::now()->subDays($days);
        $products = DB::table('catalog_product_company')
        ->select('catalog_product_company.*')
        ->join('catalog_product_company_sale', 'catalog_product_company.id', '=', 'catalog_product_company_sale.catalog_product_company_id')
        ->join('sales', 'catalog_product_company_sale.sale_id', '=', 'sales.id')
        ->whereDate('sales.created_at', '<=', $date_to_reactivate)
        ->distinct()
        ->get();

        $super_admins = User::whereIn('id', [1,2,3])->get();
        $sellers = User::where('employee_properties->department', 'Ventas')->where('is_active', 1)->get();

        // notify users
        foreach ($sellers as $seller) {
            $seller->notify(new ReactivateProductSaleNotification($products, $days));
        }
        foreach ($super_admins as $super) {
            $super->notify(new ReactivateProductSaleNotification($products, $days));
        }
        
        Log::info('app:reactivate-product-sale executed successfully.');
        Log::info($products);
    }
}
