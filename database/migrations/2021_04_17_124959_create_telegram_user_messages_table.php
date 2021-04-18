<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramUserMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_user_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->longText('message');
            $table->boolean('is_sent')->default(0);
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id')->references('id')->on('telegram_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign('author_id')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('telegram_user_messages');
        Schema::enableForeignKeyConstraints();
    }
}
