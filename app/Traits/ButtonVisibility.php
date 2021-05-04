<?php

namespace App\Traits;

trait ButtonVisibility
{
    /**
     * @return bool
     */
    public function getUpdateButtonVisibleAttribute(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function getDeleteButtonVisibleAttribute(): bool
    {
        return true;
    }
}
