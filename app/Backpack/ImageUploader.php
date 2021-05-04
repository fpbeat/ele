<?php

namespace App\Backpack;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class ImageUploader
{
    /**
     * @var string
     */
    public const STORAGE_DISK = 'public';

    /**
     * @var int
     */
    private const IMAGE_MAX_WIDTH = 640;

    /**
     * @var array
     */
    public const ALLOWED_EXTENSIONS = ['jpg', 'png', 'gif', 'tiff'];

    /**
     * @param string|null $attribute
     * @param string|null $value
     * @param string $uploadDirectory
     * @return string|null
     */
    public function upload(?string $attribute, ?string $value, string $uploadDirectory): ?string
    {
        if ($value === null) {
            Storage::disk(static::STORAGE_DISK)->delete($attribute);

            return null;
        }

        if (Str::startsWith($value, 'data:image')) {
            $filename = $uploadDirectory . '/' . $this->getRandomFileName($value);

            $image = Image::make($value)
                ->encode($this->getExtensionImageBase($value))
                ->resize(static::IMAGE_MAX_WIDTH, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            Storage::disk(static::STORAGE_DISK)->put($filename, $image->stream());
            Storage::disk(static::STORAGE_DISK)->delete($attribute);

            return $filename;
        }

        return $attribute;
    }

    /**
     * @param $attribute
     * @param string $attributeName
     * @param string $uploadDirectory
     * @return false|string
     */
    public function uploadMultiple($attribute, string $attributeName, string $uploadDirectory)
    {
        $attributeValue = is_array($attribute) ? $attribute : json_decode($attribute, true) ?? [];

        foreach (request()->get('clear_' . $attributeName, []) as $filename) {
            \Storage::disk(static::STORAGE_DISK)->delete($filename);

            $attributeValue = Arr::where($attributeValue, fn($value) => $value != $filename);
        }

        if (request()->hasFile($attributeName)) {
            foreach (request()->file($attributeName) as $file) {
                if ($file->isValid()) {
                    $image = Image::make($file->path())
                        ->resize(static::IMAGE_MAX_WIDTH, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });

                    $filename = $uploadDirectory . '/' . $this->getRandomFileName($file, $file->getClientOriginalExtension());
                    Storage::disk(static::STORAGE_DISK)->put($filename, $image->stream());

                    $attributeValue[] = $filename;
                }
            }
        }

        return json_encode($attributeValue);
    }

    /**
     * @param string $value
     * @return string|null
     */
    public function getExtensionImageBase(string $value): ?string
    {
        if (preg_match('/^data:(.*?);/i', $value, $match)) {
            switch ($match[1]) {
                case 'gif':
                case 'image/gif':
                    return 'gif';

                case 'png':
                case 'image/png':
                case 'image/x-png':
                    return 'png';

                case 'jpg':
                case 'jpeg':
                case 'image/jpg':
                case 'image/jpeg':
                    return 'jpg';

                case 'tif':
                case 'tiff':
                case 'image/tiff':
                case 'image/tif':
                case 'image/x-tif':
                case 'image/x-tiff':
                    return 'tiff';
            }
        }

        return null;
    }

    /**
     * @param $value
     * @param string|null $extension
     * @return string
     */
    public function getRandomFileName($value, ?string $extension = null): string
    {
        return sprintf('%s.%s', md5(Str::random(128)), $extension ?? $this->getExtensionImageBase($value));
    }
}
