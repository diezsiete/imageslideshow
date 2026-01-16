<?php

namespace PrestaShop\Module\ImageSlideshow\Controller;

use PrestaShop\Module\ImageSlideshow\Form\ImageTempType;
use PrestaShop\Module\ImageSlideshow\Service\ImageManager;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageController extends ImageSlideshowAdminController
{
    public function uploadTempAction(Request $request, ImageManager $imageManager): JsonResponse
    {
        $response = [];
        $status = 200;
        $form = $this->createForm(ImageTempType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $error = null;
            if ($form->isValid()) {
                try {
                    /** @var UploadedFile $uploadedFile */
                    $uploadedFile = $form->getData();
                    $path = $imageManager->moveUploadedImage($uploadedFile, $uploadedFile->getClientOriginalName());
                    $name = $uploadedFile->getClientOriginalName();
                    $response = [
                        'name' => $name,
                        'path' => "temp/$name",
                        'path_full' => $path
                    ];
                } catch (ImageUploadException|MemoryLimitException|UploadedImageConstraintException $e) {
                    $error = $e->getMessage();
                }
            } else {
                $error = $this->getFirstErrorFromForm($form);
            }
            if ($error) {
                $response = ['error' => $error];
                $status = 400;
            }
        }

        return $this->json($response, $status);
    }

    public function fetchAction(string $location, string $fileName): StreamedResponse
    {
        $file = null;
        $dirs = $location === 'temp' ? _PS_TMP_IMG_DIR_ : _PS_ROOT_DIR_ . '/' . ltrim($location, '/');

        // if fileName comes with subdir extract it and append to dirs
        if (preg_match('~^(.+?)/[^/]+$~', $fileName, $matches)) {
            $dirs .= '/' . $matches[1];
            $fileName = str_replace($matches[1] . '/', '', $fileName);
        }
        foreach ((new Finder())->files()->name($fileName)->in($dirs) as $finderFile) {
            $file = $finderFile;
        }
        if ($file) {
            return $this->renderStream(fn() => fopen($file, 'r+'), 'image');
        } else {
            throw $this->createNotFoundException();
        }
    }
}
