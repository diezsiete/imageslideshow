<?php

namespace PrestaShop\Module\ImageSlideshow\Form;

use PrestaShop\Module\ImageSlideshow\Service\ImageManager;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Router;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SlideType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface     $translator,
        array                   $locales,
        private readonly Router $router,
    ) {
        parent::__construct($translator, $locales);
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('image', FileUploadType::class, [
            //     'constraints' => [
            //         new Assert\NotNull(['message' => $this->trans('Image is required', 'Modules.Imageslideshow.Imageslideshow')]),
            //     ],
            //     'dir_final' => ImageManager::getImagesDir(),
            //     'label' => $this->trans('Image', 'Admin.Global'),
            // ])
            ->add('image', SlideEditorImageType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => $this->trans('Image is required', 'Modules.Imageslideshow.Imageslideshow')]),
                ],
            ])
            ->add('imageMobile', FileUploadType::class, [
                'dir_final' => ImageManager::getImagesDir(),
                // TODO translate spanish: Imagen movil
                'label' => $this->trans('Image mobile', 'Modules.Imageslideshow.Imageslideshow'),
                'required' => false,
            ])
            ->add('title', FormType\TextType::class, [
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                    ]),
                ],
                'label' => 'Título',
                'required' => false,
            ])
            ->add('url', FormType\TextType::class, [
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                    ]),
                ],
                'label' => 'URL',
                'required' => false,
            ])

            ->add('legend', FormType\TextType::class, [
                'constraints' => [
                    new Assert\Length([
                        'max' => 255,
                    ]),
                ],
                'label' => 'Leyenda',
                'required' => false
            ])

            // ->add('description', FormattedTextareaType::class, [
            //     'constraints' => [
            //         new CleanHtml([
            //             'message' => $this->trans(
            //                 '%s is invalid.',
            //                 'Admin.Notifications.Error'
            //             ),
            //         ]),
            //     ],
            //     'label' => $this->trans('Description', 'Admin.Global'),
            //     'required' => false,
            // ])
            ->add('description', FormType\HiddenType::class)
            ->add('targetBlank', SwitchType::class, [
                'label' => 'Abrir en ventana aparte',
                'required' => false,
                'help' => 'Hacer que el link abra en ventana aparte'
            ])
            ->add('inset', FormType\HiddenType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            $form->add('active', SwitchType::class, [
                'label' => 'Activado',
                'required' => false,
                'data' => !$data || ($data['active'] ?? true),
            ]);
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['dzdUrl'] = $this->router->generate('imageslideshow_image_upload_temp');
        $view->vars['dzdFetchUrl'] = $this->router->generate('imageslideshow_image_fetch', [
            'location' => 'location',
            'fileName' => 'fileName'
        ]);
    }
}
