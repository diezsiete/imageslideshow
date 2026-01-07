<?php

namespace PrestaShop\Module\ImageSlideshow\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\Module\ImageSlideshow\Entity\ImageSlideshow;
use PrestaShop\Module\ImageSlideshow\Grid\ImageSlideshowFilters;
use PrestaShop\Module\ImageSlideshow\Repository\ImageSlideshowRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @property EntityManagerInterface em
 * @property ImageSlideshowRepository imageSlideshowRepo
 */
class ImageSlideshowController extends ImageSlideshowAdminController
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
            // TODO translate spanish
            'layoutTitle' => $this->trans('Create Image Slideshow', domain: 'Modules.Imageslideshow.Imageslideshow') // crear carrusel de imagenes
        ]);
    }

    public function editAction(
        Request              $request,
        int                  $idImageSlideshow,
        #[Autowire(service: 'prestashop.module.imageslideshow.form.builder.slideshow')]
        FormBuilderInterface $formBuilder,
        #[Autowire(service: 'prestashop.module.imageslideshow.form.handler.slideshow')]
        FormHandlerInterface $formHandler
    ): Response
    {
        if (!$this->imageSlideshowRepo->exists($idImageSlideshow)) {
            $this->addFlash('error', $this->trans('The object cannot be loaded (or found).', domain: 'Admin.Notifications.Error'));
            return $this->redirectToRoute('imageslideshow_index');
        }


        $form = $formBuilder->getFormFor($idImageSlideshow)->handleRequest($request);
        try {
            $result = $formHandler->handleFor($idImageSlideshow, $form);

            if ($result->isSubmitted() && $result->isValid()) {
                // TODO $this->widgetWarmer->slideshow($this->imageSlideshowRepo->getSlug($idImageSlideshow));
                $this->addFlash('success', $this->trans('Successful update', [], 'Admin.Notifications.Success'));
                return $this->redirectToRoute('imageslideshow_index');
            }
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('@Modules/imageslideshow/views/templates/admin/upsert.html.twig', [
            'form' => $form->createView(),
            // TODO translate spanish
            'layoutTitle' => $this->trans('Edit Image Slideshow', domain: 'Modules.Imageslideshow.Imageslideshow') // editar carrusel de imagenes
        ]);
    }

    public function deleteAction(int $idImageSlideshow): RedirectResponse
    {
        try {
            $entity = $this->imageSlideshowRepo->find($idImageSlideshow);
            if ($entity) {
                $this->em->remove($entity);
                $this->em->flush();
                $this->addFlash('success', $this->trans('Successful deletion.', domain: 'Admin.Notifications.Success'));
            } else {
                // TODO translation
                $this->addFlash('error', sprintf("Entidad %s no existe", $idImageSlideshow));
            }
        } catch (Throwable $exception) {
            $this->addFlash('error', $exception->getMessage());
            $entity = null;
        }

        return $this->redirectToRoute('imageslideshow_index');
    }

    public function toggleStatusAction(int $idImageSlideshow): RedirectResponse
    {
        try {
            /** @var ImageSlideshow $entity */
            $entity = $this->imageSlideshowRepo->find($idImageSlideshow);
            if ($entity) {
                $entity->toggle();
                $this->em->flush();
                $this->addFlash('success', $this->trans(
                    'The status has been successfully updated.', domain: 'Admin.Notifications.Success'
                ));
            } else {
                // TODO translation
                $this->addFlash('error', sprintf("Entidad %s no existe", $id));
            }
        } catch (Throwable $exception) {
            $this->addFlash('error', $exception->getMessage());
            $entity = null;
        }

        return $this->redirectToRoute('imageslideshow_index');
    }
}
