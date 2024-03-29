<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Contact;
use App\Models\Payroll;
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
        User::create([
            'name' => 'Jorge Sherman',
            'email' => 'j.sherman@emblemas3d.com',
            'password' => bcrypt('e3dusaJ'),
        ]);
        User::create([
            'name' => 'Claudia Maribel Ortíz González',
            'email' => 'maribel@emblemas3d.com',
            'password' => bcrypt('e3dusaM'),
        ]);

        $this->call(ProductionCostSeeder::class);
        $this->call(JustificationEventSeeder::class);
        $this->call(BonusSeeder::class);
        $this->call(HolidaySeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(DesignTypeSeeder::class);

        // create current payroll
        Payroll::create([
            'start_date' => today()->toDateString(),
            'week' => today()->weekOfYear,
        ]);
    }
}
