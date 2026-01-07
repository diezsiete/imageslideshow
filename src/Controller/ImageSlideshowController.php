<?php

namespace PrestaShop\Module\ImageSlideshow\Controller;

use PrestaShop\Module\ImageSlideshow\Grid\ImageSlideshowFilters;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;

class ImageSlideshowController extends PrestaShopAdminController
{
    public function indexAction(
        #[Autowire(service: 'prestashop.module.imageslideshow.grid.grid_factory.imageslideshow')]
        GridFactoryInterface  $gridFactory,
        ImageSlideshowFilters $filters
    ): Response
    {
        $grid = $gridFactory->getGrid($filters);
        return $this->render('@Modules/imageslideshow/views/templates/admin/index.html.twig', [
            'layoutTitle' => 'Image Slideshow',
            'grid' => $this->presentGrid($grid),
        ]);
    }
}
