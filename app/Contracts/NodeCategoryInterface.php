<?php

namespace App\Contracts;

/**
 * @property string $clean_description
 * @property string $image
 * @property bool $has_long_description
 */
interface NodeCategoryInterface
{
    /**
     * @var int
     */
    const LONG_DESCRIPTION_LENGTH = 1024;
}
