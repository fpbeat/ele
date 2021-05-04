<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('categories')->insert(array_merge(config('seeders.categories'), [
            'name' => trans(config('seeders.categories.name')),
            'created_at' => now(),
            'updated_at' => now()
        ]));
    }
}
