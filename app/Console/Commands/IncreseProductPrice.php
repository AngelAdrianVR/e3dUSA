<?php

namespace App\Console\Commands;

use App\Models\CatalogProductCompany;
use App\Models\User;
use App\Notifications\IncreaseProductPriceNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class IncreaseProductPrice extends Command
{
    protected $signature = 'app:increase-product-price';
    protected $description = 'check if a product has more than 9 months with same price';

    public function handle()
    {
        $nineMonthsAgo = Carbon::now()->subMonths(9);
        $products = CatalogProductCompany::whereDate('new_date', '<=', $nineMonthsAgo)->get();

        if ($products->count()) {
            $super_admins = User::whereIn('id', [1, 2, 3])->get();
            $sellers = User::where('employee_properties->department', 'Ventas')->where('is_active', 1)->get();

            // notify users
            foreach ($sellers as $seller) {
                $seller->notify(new IncreaseProductPriceNotification($products));
            }
            foreach ($super_admins as $super) {
                $super->notify(new IncreaseProductPriceNotification($products));
            }

            Log::info('app:increase-product-price executed successfully. There where ' . $products->count() . ' product(s)');
        } else {
            Log::info('app:increase-product-price executed successfully. There where 0 product(s)');
        }
    }
}
