<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

trait BackpackImageTrait {
    protected function uploadImage($attribute, $value) {

        $disk = config('backpack.base.root_disk_name');

        $destination_path = "public/uploads";

        if ($value === null) {
            \Storage::disk($disk)->delete($attribute);

            return null;
        }

        if (Str::startsWith($value, 'data:image')) {
            $image = Image::make($value)->encode('jpg');

            $filename = md5($value . time()) . '.jpg';

            \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());
            \Storage::disk($disk)->delete($attribute);

            return $destination_path  . '/' . $filename;
        }

        return null;
    }
}
