<?php

namespace PrestaShop\Module\ImageSlideshow\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShop\Module\ImageSlideshow\Repository\ImageSlideshowRepository")
 */
class ImageSlideshow
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_image_slideshow", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=132)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=132)
     */
    private ?string $slug = null;

    /**
     * @ORM\Column(name="id_shop", type="integer")
     */
    private int $shop = 1;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active = true;

    /**
     * @var ImageSlideshowSlide[]|Collection
     * @ORM\OneToMany(mappedBy="slideshow", targetEntity="PrestaShop\Module\ImageSlideshow\Entity\ImageSlideshowSlide", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private Collection|array $slides;

    public function __construct()
    {
        $this->slides = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
    public function setActive(bool $active): static
    {
        $this->active = $active;
        return $this;
    }
    public function toggle(): static
    {
        $this->active = !$this->active;
        return $this;
    }

    /**
     * @return ImageSlideshowSlide[]|Collection
     */
    public function getSlides(): Collection|array
    {
        return $this->slides;
    }
    /**
     * @return ImageSlideshowSlide[]
     */
    public function getActiveSlides(): array
    {
        $activeSlides = [];
        foreach ($this->slides as $slide) {
            if ($slide->isActive()) {
                $activeSlides[] = $slide;
            }
        }
        return $activeSlides;
    }
    public function addSlide(ImageSlideshowSlide $slide): static
    {
        if (!$this->slides->contains($slide)) {
            $this->slides[] = $slide;
            $slide->setSlideshow($this);
        }
        return $this;
    }
    public function getSlide(int $id): ?ImageSlideshowSlide
    {
        foreach ($this->getSlides() as $slide) {
            if ($slide->getId() === $id) {
                return $slide;
            }
        }
        return null;
    }
}
