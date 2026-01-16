<?php

namespace PrestaShop\Module\ImageSlideshow\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Router;

class FileUploadType extends AbstractType
{
    public function __construct(
        private readonly Router $router
    ){}

    public function getParent(): string
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'dropzoned custom-file-input dummy-input',
                // TODO translation
                'placeholder' => 'Seleccionar archivo',
                'autocomplete' => 'off',
            ],
            'build_view' => null,
            'data_class' => null,
            'dir_final' => '',
            'upload_route' => 'imageslideshow_image_upload_temp',
        ]);

        $resolver->setNormalizer('dir_final', fn (Options $options, string $value) => ltrim($value, '/'));
    }

    public function getBlockPrefix(): string
    {
        return 'file_upload';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $fileName = null;
        if ($data = $view->vars['data'] ?? null) {
            $fileName = $data;
            $view->vars['attr']['placeholder'] = $data;
        }
        $view->vars['extra'] = [
            'label' => $options['label'] ?: 'Imagen',
            'datas' => [
                'data-file-fetch-url' => $this->router->generate('imageslideshow_image_fetch', [
                    'location' => 'location',
                    'fileName' => 'fileName'
                ]),
                'data-file-upload-url' => $this->router->generate($options['upload_route']),
                'data-file-name' => $fileName,
                'data-file-name-original' => $fileName,
            ],
        ];
        if ($help = $options['help'] ?? '') {
            $view->vars['extra']['help'] = $help;
        }

        if ($data = $view->vars['data'] ?? null) {
            if (!str_contains($data, 'temp/')) {
                $nameOriginal = ($options['dir_final'] ? $options['dir_final'] . '/' : '') . $data;
                $placeholder = preg_replace(
                    '/(.+)(-\w+)(\.\w+$)/', '$1$3', $data
                );
            } else {
                $nameOriginal = $data;
                $placeholder = str_replace('temp/', '', $data);
            }
            $view->vars['extra']['datas']['data-file-name-original'] = $nameOriginal;
            $view->vars['attr']['placeholder'] = $placeholder;
        }

        if ($options['build_view']) {
            $options['build_view']($view, $form, $options);
        }
    }
}
