<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class CatalogCategory extends Model
{
    use CrudTrait;

    /**
     * @var string
     */
    protected $table = 'catalog_category';
}
