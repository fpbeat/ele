<?php

namespace App\Models;

use App\Backpack\ImageUploader;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class PageTypes extends Model
{
    use CrudTrait;
}
