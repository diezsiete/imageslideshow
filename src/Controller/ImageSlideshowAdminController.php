<?php

namespace PrestaShop\Module\ImageSlideshow\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\Module\ImageSlideshow\Repository\ImageSlideshowRepository;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;

/**
 * @property EntityManagerInterface em
 * @property ImageSlideshowRepository imageSlideshowRepo
 */
abstract class ImageSlideshowAdminController extends PrestaShopAdminController
{
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            'em' => EntityManagerInterface::class,
            'imageSlideshowRepo' => ImageSlideshowRepository::class,
        ];
    }

    public function __get(string $name)
    {
        return $this->container->get($name);
    }
}
