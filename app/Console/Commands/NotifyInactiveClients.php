<?php

namespace App\Console\Commands;

use App\Models\CompanyBranch;
use App\Models\User;
use App\Notifications\InactiveClientsNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyInactiveClients extends Command
{
    protected $signature = 'app:inactive-clients';
    protected $description = 'Notify sales users about inactive clients';

    public function handle()
    {
        $inactiveClients = CompanyBranch::where('days_to_reactivate', '>', 0)
            ->get()
            ->filter(function ($customerBranch) {
                return !$this->hasRecentActivity($customerBranch);
            });

        if ($inactiveClients->count()) {
            foreach ($this->getSalesUsers() as $user) {
                $user->notify(new InactiveClientsNotification($inactiveClients));
            }
        }

        $this->info('Inactive clients notification sent successfully.');
        Log::info('app:inactive-clients executed successfully. There where ' . $inactiveClients->count() . ' inactive client(s)');
    }
    
    protected function getSalesUsers()
    {
        return User::whereIn('id', [1, 2, 3])->orWhere('employee_properties->department', 'Ventas')->get();
    }

    protected function hasRecentActivity(CompanyBranch $customerBranch)
    {
        $threshold = now()->subDays($customerBranch->days_to_reactivate);
        
        return $customerBranch->quotes()->whereDate('created_at', '>=', $threshold)->get()->count()
            || $customerBranch->sales()->whereDate('created_at', '>=', $threshold)->get()->count()
            || $customerBranch->samples()->whereDate('created_at', '>=', $threshold)->get()->count();
    }
}
