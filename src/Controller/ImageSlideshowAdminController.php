<?php

namespace PrestaShop\Module\ImageSlideshow\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\Module\ImageSlideshow\Repository\ImageSlideshowRepository;
use PrestaShop\Module\ImageSlideshow\Repository\ImageSlideshowSlideRepository;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * @property EntityManagerInterface em
 * @property ImageSlideshowRepository imageSlideshowRepo
 * @property ImageSlideshowSlideRepository  slideRepo
 */
abstract class ImageSlideshowAdminController extends PrestaShopAdminController
{
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            'em' => EntityManagerInterface::class,
            'imageSlideshowRepo' => ImageSlideshowRepository::class,
            'slideRepo' => ImageSlideshowSlideRepository::class,
        ];
    }

    public function __get(string $name)
    {
        return $this->container->get($name);
    }

    public function deleteEntity(object|null $entity): bool
    {
        try {
            if ($entity) {
                $this->em->remove($entity);
                $this->em->flush();
                $this->addFlash('success', $this->trans('Successful deletion.', domain: 'Admin.Notifications.Success'));
                return true;
            } else {
                $this->addFlash('error', $this->trans('The object cannot be loaded (or found).', domain: 'Admin.Notifications.Error'));
            }
        } catch (Throwable $exception) {
            $this->addFlash('error', $exception->getMessage());
        }
        return false;
    }

    protected function getFirstErrorFromForm(FormInterface $form): ?string
    {
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                foreach($childForm->getErrors() as $error) {
                    return $error->getMessage();
                }
            }
        }
        foreach($form->getErrors() as $error) {
            return $error->getMessage();
        }
        return null;
    }

    protected function renderStream($callbackStream, $contentType = 'application/pdf', $dispositionAttachment = null): StreamedResponse
    {
        $response = new StreamedResponse(function() use ($callbackStream){
            $outputStream = fopen('php://output', 'wb');
            $fileStream = $callbackStream();
            stream_copy_to_stream($fileStream, $outputStream);
        });
        if ($contentType) {
            $response->headers->set('Content-Type', $contentType);
        }
        if ($dispositionAttachment) {
            $disposition = HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $dispositionAttachment);
            $response->headers->set('Content-Disposition', $disposition);
        }
        return $response;
    }
}
