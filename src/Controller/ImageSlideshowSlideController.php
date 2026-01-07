<?php

namespace PrestaShop\Module\ImageSlideshow\Controller;

use Symfony\Component\HttpFoundation\Response;

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
}
