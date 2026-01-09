<?php

namespace PrestaShop\Module\ImageSlideshow\Form;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\Module\ImageSlideshow\Entity\ImageSlideshow;
use PrestaShop\Module\ImageSlideshow\Repository\ImageSlideshowRepository;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class SlideshowFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly ImageSlideshowRepository  $imageSlideshowRepo,
        private readonly EntityManagerInterface    $em,
        private readonly PropertyAccessorInterface $accessor,
    ){}

    public function create(array $data): ?int
    {
        $slideshow = new ImageSlideshow();
        foreach ($data as $prop => $val) {
            $this->accessor->setValue($slideshow, $prop, $val);
        }
        $this->em->persist($slideshow);
        $this->em->flush();
        return $slideshow->getId();
    }

    public function update($id, array $data): void
    {
        if ($slideshow = $this->imageSlideshowRepo->find($id)) {
            foreach ($data as $prop => $val) {
                if ($this->accessor->isWritable($slideshow, $prop)) {
                    $this->accessor->setValue($slideshow, $prop, $val);
                }
            }
            $this->em->flush();
        }
    }
}
