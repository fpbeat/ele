<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessagesSeeder extends Seeder
{
    /**
     * @return void
     */
    public function run(): void
    {
        DB::table('messages')->truncate();

        foreach (config('seeders.messages') as $message) {
            DB::table('messages')->insert(array_merge($message, [
                'message' => trans($message['message']),
            ]));
        }
    }
}
