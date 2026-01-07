<?php

namespace PrestaShop\Module\ImageSlideshow\Form;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

class ImageSlideshowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $slugDisabled = (bool)($options['data']['id'] ?? false);
        $builder
            ->add('name', FormType\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => 128,
                    ]),
                ],
                // TODO translation see \PrestaShopBundle\Form\Admin\Improve\Design\Pages\CmsPageCategoryType
                'label' => 'Nombre',
            ])
            ->add('slug', FormType\TextType::class, [
                'attr' => [
                    'class' => 'js-copier-destination-friendly-url',
                ],
                'disabled' => $slugDisabled,
                'constraints' => [
                    new IsUrlRewrite(),
                    new Assert\Length([
                        'max' => 132,
                    ]),
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $slideshow = $event->getData();
            $form->add('active', SwitchType::class, [
                // TODO translation see \PrestaShopBundle\Form\Admin\Improve\Design\Pages\CmsPageCategoryType
                'label' => 'Activado',
                'required' => false,
                'data' => !$slideshow || $slideshow['active'],
            ]);
        });
    }
}
