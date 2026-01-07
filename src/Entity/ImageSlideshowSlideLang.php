<?php

namespace PrestaShop\Module\ImageSlideshow\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class ImageSlideshowSlideLang
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PrestaShop\Module\ImageSlideshow\Entity\ImageSlideshowSlide", inversedBy="lang")
     * @ORM\JoinColumn(name="id_image_slideshow_slide", referencedColumnName="id_image_slideshow_slide")
     */
    private ?ImageSlideshowSlide $slide = null;

    /**
     * @ORM\Id
     * @ORM\Column(name="id_lang", type="integer")
     */
    private int $lang = 1;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $legend = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $url = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $image = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $imageMobile = null;

    public function getSlide(): ?ImageSlideshowSlide
    {
        return $this->slide;
    }

    public function setParent(ImageSlideshowSlide $parent): static
    {
        $this->slide = $parent;
        return $this;
    }

    public function getLang(): int
    {
        return $this->lang;
    }
    public function setLang(int $lang): static
    {
        $this->lang = $lang;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getLegend(): ?string
    {
        return $this->legend;
    }
    public function setLegend(?string $legend): static
    {
        $this->legend = $legend;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
    public function setUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }
    public function getImageMobile(): ?string
    {
        return $this->imageMobile;
    }
    public function setImageMobile(?string $imageMobile): static
    {
        $this->imageMobile = $imageMobile;
        return $this;
    }
}
