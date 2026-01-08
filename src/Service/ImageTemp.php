<?php

namespace PrestaShop\Module\ImageSlideshow\Service;

use ImageManager;

class ImageTemp
{
    public function moveTempImage(?string $tmpPath, string $definitiveDir, ?string $ext = null): ?string
    {
        $uniqName = null;
        if ($tempImage = $this->getImageInTemp($tmpPath)) {
            // replace spaces to avoid warnings in browser: Failed parsing 'srcset' attribute value since it has an unknown descriptor
            $pathinfo = pathinfo($tempImage);
            $uniqName = preg_replace('/\s+/', '-', trim($pathinfo['filename'])) . '-' . uniqid() . '.' . ($ext ?? $pathinfo['extension']);
            // ensure dir exists
            if (!file_exists($definitiveDir)) {
                mkdir($definitiveDir, recursive: true);
            }

            ImageManager::resize(
                _PS_TMP_IMG_DIR_ . $tempImage, $this->getDifinitivePath($definitiveDir, $uniqName), forceType: true
            );
        }
        return $uniqName;
    }


    public function removeDefinitiveImage(string $definitiveDir, string $imageName): void
    {
        $imagePath = $this->getDifinitivePath($definitiveDir, $imageName);
        if (file_exists($imagePath)) {
            unlink($imagePath);
            if (($webpPath = $this->convertNameToWebp($imagePath)) && file_exists($webpPath)) {
                unlink($webpPath);
            }
        }
    }

    public function getImageInTemp(?string $tempPath): ?string
    {
        if ($tempPath) {
            $tempPathExplode = explode('/', $tempPath);
            if ($tempPathExplode[0] === 'temp') {
                return $tempPathExplode[1];
            }
        }
        return null;
    }

    private function convertNameToWebp(string $name): ?string
    {
        return ($dotpos = strrpos($name, '.')) !== false ? substr($name, 0, $dotpos) . '.webp' : null;
    }

    public function getDifinitivePath(string $definitiveDir, string $definitiveName): string
    {
        return $definitiveDir . '/' . ltrim('/', $definitiveName);
    }
}
