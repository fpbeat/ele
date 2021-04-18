<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagesSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('pages')->truncate();

        DB::table('pages')->insert(array_merge(config('seeders.pages'), [
            'name' => trans(config('seeders.pages.name')),
            'created_at' => now(),
            'updated_at' => now()
        ]));
    }
}
