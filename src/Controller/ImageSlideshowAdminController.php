<?php

namespace PrestaShop\Module\ImageSlideshow\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\Module\ImageSlideshow\Repository\ImageSlideshowRepository;
use PrestaShop\Module\ImageSlideshow\Repository\ImageSlideshowSlideRepository;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;

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
}
