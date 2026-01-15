<?php

namespace PrestaShop\Module\ImageSlideshow\Controller;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ImageSlideshowSlideController extends ImageSlideshowAdminController
{
    public function slidesAction(int $idImageSlideshow): Response
    {
        $slideshow = $this->imageSlideshowRepo->findWithSlides($idImageSlideshow);

        if (!$slideshow) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found).', domain: 'Admin.Notifications.Error'));
            return $this->redirectToRoute('imageslideshow_index');
        }

        return $this->render('@Modules/imageslideshow/views/templates/admin/slides.html.twig', [
            'slideshow' => $slideshow,
            'slides' => $slideshow->getSlides(),
            // TODO translate spanish : Carrusel %name%
            'layoutTitle' => $this->trans('Slideshow %name%', ['%name%' => strtolower($slideshow->getName())], 'Modules.Imageslideshow.Imageslideshow')
        ]);
    }

    public function addAction(
        Request $request,
        int $idImageSlideshow,
        #[Autowire(service: 'prestashop.module.imageslideshow.form.builder.slide')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.module.imageslideshow.form.handler.slide')]
        FormHandlerInterface $formHandler
    ): Response
    {
        if (!$this->imageSlideshowRepo->exists($idImageSlideshow)) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found).', domain: 'Admin.Notifications.Error'));
            return $this->redirectToRoute('imageslideshow_index');
        }

        $form = $formBuilder->getForm()->handleRequest($request);

        try {
            $result = $formHandler->handle($form);

            if (null !== $result->getIdentifiableObjectId()) {
                // TODO $this->widgetWarmer->slideshow($this->imageSlideshowRepo->getSlug($idImageSlideshow));
                $this->addFlash('success', $this->trans('Successful creation', domain: 'Admin.Notifications.Success'));
                return $this->redirectToRoute('imageslideshow_slides', ['idImageSlideshow' => $idImageSlideshow]);
            }
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('@Modules/imageslideshow/views/templates/admin/upsert-slide.html.twig', [
            'form' => $form->createView(),
            'idImageSlideshow' => $idImageSlideshow,
            'image' => null,
            // TODO translate spanish : crear diapositiva
            'layoutTitle' => $this->trans('Create Slide', domain: 'Modules.Imageslideshow.Imageslideshow')
        ]);
    }

    public function editAction(
        Request              $request,
        int                  $idImageSlideshow,
        int                  $idImageSlideshowSlide,
        #[Autowire(service: 'prestashop.module.imageslideshow.form.builder.slide')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.module.imageslideshow.form.handler.slide')]
        FormHandlerInterface $formHandler
    ): Response
    {
        if (!$this->slideRepo->exists($idImageSlideshowSlide, $idImageSlideshow)) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found).', domain: 'Admin.Notifications.Error'));
            return $this->redirectToRoute('imageslideshow_index');
        }

        $form = $formBuilder->getFormFor($idImageSlideshowSlide)->handleRequest($request);
        try {
            $result = $formHandler->handleFor($idImageSlideshowSlide, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                // TODO $this->widgetWarmer->slideshow($this->imageSlideshowRepo->getSlug($idImageSlideshow));
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
                return $this->redirectToRoute('imageslideshow_slides', ['idImageSlideshow' => $idImageSlideshow]);
            }
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('@Modules/imageslideshow/views/templates/admin/upsert-slide.html.twig', [
            'form' => $form->createView(),
            'idImageSlideshow' => $idImageSlideshow,
            'image' => null,
            // TODO translate spanish : Editar diapositiva
            'layoutTitle' => $this->trans('Edit Slide', domain: 'Modules.Imageslideshow.Imageslideshow')
        ]);
    }
}
