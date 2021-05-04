<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitTypesSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        foreach (config('seeders.unit_types') as $type) {
            DB::table('unit_types')->insert([
                'name' => trans($type),
            ]);
        }
    }
}
