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
    public function getParent(): string
    {
        return HiddenType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($fileName = $view->vars['data'] ?? null) {
            $view->vars['attr']['data-file-name'] = !str_contains($fileName, 'temp/')
                ? ltrim(ImageManager::getImagesDir(), '/') . '/' . $fileName
                : $fileName;
        }
    }
}
