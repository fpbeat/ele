<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->integer('amount');
            $table->unsignedBigInteger('unit_id')->nullable();

            $table->double('price', 10, 2);

            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->longText('extra_images')->nullable();

            $table->longText('ingredients')->nullable();
            $table->boolean('active')->default(1);

            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('unit_types')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogs');
    }
}
