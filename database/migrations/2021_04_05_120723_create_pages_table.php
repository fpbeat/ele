<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('type_id');
            $table->string('name');

            $table->unsignedInteger('lft')->default(0);
            $table->unsignedInteger('rgt')->default(0);
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('depth');

            $table->longText('buttons')->nullable();
            $table->unsignedTinyInteger('buttons_per_row')->default(1);

            $table->longText('description')->nullable();
            $table->string('image')->nullable();

            $table->timestamps();

            $table->index(['lft', 'rgt', 'parent_id']);
            $table->foreign('type_id')->references('id')->on('page_types')->onUpdate('NO ACTION')->onDelete('CASCADE');
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
        Schema::dropIfExists('pages');
        Schema::enableForeignKeyConstraints();
    }
}
