<?php

namespace App\Console\Commands;

use App\Models\RawMaterial;
use Illuminate\Console\Command;

class UpdatePartNumber extends Command
{
    protected $signature = 'app:update-part-number';
    protected $description = 'Update the "part_number" column of the "raw_materials" table.';

    public function handle()
    {
        $rawMaterials = RawMaterial::all();

        foreach ($rawMaterials as $rawMaterial) {
            $id = str_pad($rawMaterial->id, 4, '0', STR_PAD_LEFT);
            $exploded = explode('-', $rawMaterial->part_number);
            $exploded[2] = $id;
            $new_part_number = implode('-', $exploded);
            $rawMaterial->update(['part_number' => $new_part_number]);
        }

        $this->info('Part numbers updated successfully.');
    }
}
