<?php

namespace App\Services;

use App\Services\Contracts\PhotoService as IPhotoService;
use Illuminate\Contracts\Filesystem\Factory as Filesystem;
use App\Services\Contracts\AuthService;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class PhotoService implements IPhotoService
{
    private $authService;
    private $fileSystem;

    public function __construct(Filesystem $fileSystem, AuthService $authService)
    {
        $this->fileSystem = $fileSystem;
        $this->authService = $authService;
    }

    public function crop(string $pathToPhoto, int $width, int $height): string
    {
        $croppedImage = Image::make(
            Storage::path($pathToPhoto)
        )->crop($width, $height);
        $filePath = 'cropped/'
            . Str::random(40)
            . '.'
            . $croppedImage->extension;

        $this->fileSystem->put(
            $filePath,
            $croppedImage->stream()
        );

        return $filePath;
    }
}
