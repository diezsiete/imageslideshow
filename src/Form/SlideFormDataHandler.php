<?php

namespace PrestaShop\Module\ImageSlideshow\Form;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\Module\ImageSlideshow\Entity\ImageSlideshow;
use PrestaShop\Module\ImageSlideshow\Entity\ImageSlideshowSlide;
use PrestaShop\Module\ImageSlideshow\Repository\ImageSlideshowSlideRepository;
use PrestaShop\Module\ImageSlideshow\Service\ImageTemp;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;

class SlideFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(
        private readonly ImageSlideshowSlideRepository $slideRepo,
        private readonly EntityManagerInterface        $em,
        private readonly ImageTemp                     $imageTemp,
    ){}

    public function create(array $data)
    {
        /** @var ImageSlideshow $slideshow */
        $slideshow = $data['slideshow'];
        $position = 0;
        $slide = (new ImageSlideshowSlide())
            ->setSlideshow($slideshow)
            ->setPosition($position);
        $slide = $this->fillSlide($slide, $data);

        foreach ($this->slideRepo->findSlides($slideshow) as $sibling) {
            $position++;
            $sibling->setPosition($position);
        }

        $this->em->persist($slide);
        $this->em->flush();

        return $slide->getId();
    }

    public function update($id, array $data): void
    {
        if ($slide = $this->slideRepo->findSlide($id)) {
            $this->fillSlide($slide, $data);
            $this->em->flush();
        }
    }

    private function fillSlide(ImageSlideshowSlide $slide, array $data): ImageSlideshowSlide
    {
        $slide
            ->setActive((bool)$data['active'])
            ->setTargetBlank((bool)$data['targetBlank'])
            ->getLang()
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setLegend($data['legend'])
            ->setUrl($data['url']);

        if ($image = $this->fillSlideImage($data['image'] ?? null, $slide->getLang()->getImage())) {
            $slide->getLang()->setImage($image);
        }
        if ($imageMobile = $this->fillSlideImage($data['imageMobile'] ?? null, $slide->getLang()->getImageMobile())) {
            $slide->getLang()->setImageMobile($imageMobile);
        }

        return $slide;
    }

    private function fillSlideImage(?string $dataImage, ?string $slideImage): ?string
    {
        if ($this->imageTemp->getImageInTemp($dataImage) && $slideImage) {
            $this->imageTemp->removeDefinitiveImage(ImageSlideshowSlide::getImagesPath(), $slideImage);
        }
        return $this->imageTemp->moveTempImage($dataImage, ImageSlideshowSlide::getImagesPath());
    }
}
