<?php

namespace App\Models;

use App\Traits\ButtonVisibility;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Message extends Model
{
    use CrudTrait, ButtonVisibility;

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
