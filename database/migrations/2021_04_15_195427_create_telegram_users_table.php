<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('username')->nullable();
            $table->string('full_name')->nullable();
            $table->string('language_code')->nullable();
            $table->unsignedBigInteger('last_page_id')->nullable();
            $table->boolean('locked')->default(0);
            $table->timestamps();

            $table->foreign('last_page_id')->references('id')->on('pages')->onUpdate('NO ACTION')->onDelete('SET NULL');
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
        Schema::dropIfExists('telegram_users');
        Schema::enableForeignKeyConstraints();
    }
}
