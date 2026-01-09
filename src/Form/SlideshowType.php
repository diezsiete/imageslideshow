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
use Symfony\Contracts\Translation\TranslatorInterface;

class SlideshowType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', FormType\TextType::class, [
                'attr' => [
                    'class' => 'js-copier-source-title',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => 128,
                    ]),
                ],
                'label' => $this->translator->trans('Name', domain: 'Admin.Globals'),
            ])
            ->add('slug', FormType\TextType::class, [
                'attr' => [
                    'class' => 'js-copier-destination-friendly-url',
                ],
                'constraints' => [
                    new IsUrlRewrite(),
                    new Assert\Length([
                        'max' => 132,
                    ]),
                ],
                'label' => $this->translator->trans('Slug', domain: 'Admin.Globals'),
                // TODO translation spanish : Para identificación en plantillas.
                'help' => $this->translator->trans('For template identification.', domain: 'Modules.Imageslideshow.Imageslideshow')
                    . ' '  . $this->translator->trans('Only letters and the hyphen (-) character are allowed.', domain: 'Admin.Design.Feature')
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $slideshow = $event->getData();
            $form->add('active', SwitchType::class, [
                'label' => $this->translator->trans('Active', domain: 'Admin.Globals'),
                'required' => false,
                'data' => !$slideshow || $slideshow['active'],
            ]);
        });
    }
}
