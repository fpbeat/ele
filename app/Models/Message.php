<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Message extends Model
{
    use CrudTrait;

    /**
     * @var bool
     */
    public $timestamps = FALSE;

    /**
     * @var string[]
     */
    protected $fillable = ['message'];

    /**
     * @return string
     */
    public function getCleanMessageAttribute(): string
    {
        return Str::cleanupSummernote($this->attributes['message']);
    }
}
