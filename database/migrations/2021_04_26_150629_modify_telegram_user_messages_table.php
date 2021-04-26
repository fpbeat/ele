<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTelegramUserMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('telegram_user_messages', function (Blueprint $table) {
            $table->enum('event', ['user', 'review', 'proposal'])
                ->default('user')
                ->after('is_sent');
        });
    }

    public function down()
    {
        Schema::table('telegram_user_messages', function (Blueprint $table) {
            $table->dropColumn('event');
        });
    }
}
