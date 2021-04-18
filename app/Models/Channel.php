<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use CrudTrait;

    /**
     * @var string[]
     */
    protected $fillable = ['name', 'invite_link', 'channel_id'];
}
