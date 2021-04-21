<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('settings')->insert(config('seeders.settings'));
    }
}
