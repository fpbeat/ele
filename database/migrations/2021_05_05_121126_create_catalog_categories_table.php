<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogCategoriesTable extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create('catalog_category', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('catalog_id');

            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('catalog_id')->references('id')->on('catalogs')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_category');
    }
}
