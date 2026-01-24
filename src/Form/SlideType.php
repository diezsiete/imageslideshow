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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', SlideEditorImageType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => $this->trans('Image is required', 'Modules.Imageslideshow.Imageslideshow')]),
                ],
                'label' => $this->trans('Image', 'Admin.Global'),
            ])
            ->add('description', FormType\HiddenType::class)
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
}
