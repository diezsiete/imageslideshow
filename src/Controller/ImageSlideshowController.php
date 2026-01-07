<?php

namespace PrestaShop\Module\ImageSlideshow\Controller;

use PrestaShop\Module\ImageSlideshow\Grid\ImageSlideshowFilters;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
            'layoutTitle' => $this->trans('Image Slideshows', domain: 'Modules.Imageslideshow.Imageslideshow'), // carruseles de imagenes
            'grid' => $this->presentGrid($grid),
        ]);
    }

    public function createAction(
        Request              $request,
        #[Autowire(service: 'prestashop.module.imageslideshow.form.builder.slideshow')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.module.imageslideshow.form.handler.slideshow')]
        FormHandlerInterface $formHandler
    ): Response
    {
        $form = $formBuilder->getForm()->handleRequest($request);

        try {
            $result = $formHandler->handle($form);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', domain: 'Admin.Notifications.Success'));
                return $this->redirectToRoute('imageslideshow_index');
            }
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('@Modules/imageslideshow/views/templates/admin/upsert.html.twig', [
            'form' => $form->createView(),
            'layoutTitle' => $this->trans('Create Image Slideshow', domain: 'Modules.Imageslideshow.Imageslideshow') // crear carrusel de imagenes
        ]);
    }
}
