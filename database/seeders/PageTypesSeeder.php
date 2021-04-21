<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageTypesSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        foreach (config('seeders.page_types') as $type) {
            DB::table('page_types')->insert(array_merge($type, [
                'name' => trans($type['name']),
            ]));
        }
    }
}
