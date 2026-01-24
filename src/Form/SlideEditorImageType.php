<?php

namespace PrestaShop\Module\ImageSlideshow\Form;

use PrestaShop\Module\ImageSlideshow\Service\ImageManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

class SlideEditorImageType extends AbstractType
{
    public function __construct(
        private readonly Router $router
    ){}

    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'slide_edtor_image';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['dzd']['url'] = $this->router->generate('imageslideshow_image_upload_temp');
        $view->vars['dzd']['fetchUrl'] = $this->router->generate('imageslideshow_image_fetch', [
            'location' => 'location',
            'fileName' => 'fileName'
        ]);

        if ($fileName = $view->vars['data'] ?? null) {
            $view->vars['attr']['data-file-name'] = !str_contains($fileName, 'temp/')
                ? ltrim(ImageManager::getImagesDir(), '/') . '/' . $fileName
                : $fileName;
        }
    }
}
