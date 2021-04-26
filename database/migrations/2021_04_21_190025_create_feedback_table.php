<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('type', ['review', 'proposal']);
            $table->timestamp('created_at')->nullable();

            $table->foreign('user_id')->references('id')->on('telegram_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
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
        Schema::dropIfExists('feedback');
        Schema::enableForeignKeyConstraints();
    }
}
