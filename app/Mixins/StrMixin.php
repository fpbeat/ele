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
            $html = preg_replace_callback('/<p(?:\s[^>]*)?>(.*?)<\/p>/', function ($match) {
                return trim($match[1]) . "\n";
            }, $html);

            $html = Purify::clean($html, [
                'HTML.AllowedElements' => 'b,i,u,a',
                'HTML.AllowedAttributes' => 'href',
                'AutoFormat.RemoveEmpty' => true,
                'AutoFormat.RemoveEmpty.RemoveNbsp' => true
            ]);

            return trim($html);
        };
    }

    /**
     * @return \Closure
     */
    public function cleanEmojis(): \Closure
    {
        return function ($text) {
            // Match Emoticons
            $text = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $text);

            // Match Miscellaneous Symbols and Pictographs
            $text = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $text);

            // Match Transport And Map Symbols
            $text = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $text);

            // Match Miscellaneous Symbols
            $text = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $text);

            // Match Dingbats
            $text = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $text);

            return trim($text);
        };
    }
}
