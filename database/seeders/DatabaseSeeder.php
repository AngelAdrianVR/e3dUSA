<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Contact;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //RawMaterial::factory(10)->create();
        Company::factory(6)->create();
        CompanyBranch::factory(18)->create();
        Contact::factory(54)->create();

        User::create([
            'name' => 'Super admin',
            'email' => 'admin@e3dusa.com',
            'password' => bcrypt('123123123'),
        ]);
        
        $this->call(ProductionCostSeeder::class);
    }
}
