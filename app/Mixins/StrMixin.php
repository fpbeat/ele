<?php

namespace App\Mixins;

use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;

class StrMixin
{
    /**
     * @return \Closure
     */
    public function uuidSimple(): \Closure
    {
        return function ($length = 13) {
            return strtoupper(substr(Str::uuid(), 0, $length));
        };
    }

    /**
     * @return \Closure
     */
    public function cleanupSummernote(): \Closure
    {
        return function ($html) {
            $html = preg_replace('/<p(?:\s[^>]*)?>(.*?)<\/p>/', "$1\n", $html);

            $html = Purify::clean($html, [
                'HTML.AllowedElements' => 'b,i,u,a',
                'HTML.AllowedAttributes' => 'href',
                'AutoFormat.RemoveEmpty' => true,
                'AutoFormat.RemoveEmpty.RemoveNbsp' => true
            ]);

            return trim($html);
        };
    }
}
