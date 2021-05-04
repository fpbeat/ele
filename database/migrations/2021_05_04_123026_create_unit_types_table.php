<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitTypesTable extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create('unit_types', function (Blueprint $table) {
            $table->id();

            $table->string('name');
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_types');
    }
}
