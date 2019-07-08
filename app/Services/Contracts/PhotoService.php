<?php

namespace App\Services\Contracts;

interface PhotoService
{
    public function crop(string $pathToPhoto, int $width, int $height): string;
}
