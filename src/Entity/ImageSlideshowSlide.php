<?php

namespace PrestaShop\Module\ImageSlideshow\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShop\Module\ImageSlideshow\Repository\ImageSlideshowSlideRepository")
 */
class ImageSlideshowSlide
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_image_slideshow_slide", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="PrestaShop\Module\ImageSlideshow\Entity\ImageSlideshow", inversedBy="slides")
     * @ORM\JoinColumn(name="id_image_slideshow", referencedColumnName="id_image_slideshow", onDelete="CASCADE")
     */
    private ?ImageSlideshow $slideshow;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $position = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $targetBlank = false;

    /**
     * @var ImageSlideshowSlideLang[]|Collection
     * @ORM\OneToMany(mappedBy="slide", targetEntity="PrestaShop\Module\ImageSlideshow\Entity\ImageSlideshowSlideLang", cascade={"persist", "remove"}, fetch="EAGER", orphanRemoval=true, indexBy="lang")
     */
    private Collection|array $lang;

    public static function getImagesPath(): string
    {
        return _MODULE_DIR_ . 'imageslideshow/images';
    }

    public function __construct()
    {
        $this->lang = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlideshow(): ?ImageSlideshow
    {
        return $this->slideshow;
    }
    public function setSlideshow(ImageSlideshow $slideshow): static
    {
        $this->slideshow = $slideshow;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
    public function setPosition(int $position): static
    {
        $this->position = $position;
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

    public function isTargetBlank(): bool
    {
        return $this->targetBlank;
    }

    public function setTargetBlank(bool $targetBlank): static
    {
        $this->targetBlank = $targetBlank;
        return $this;
    }

    public function getImagePath(): string
    {
        return static::getImagesPath() . '/' . $this->getLang()->getImage();
    }
    public function getImageMobilePath(): ?string
    {
        return ($imageMobile = $this->getLang()->getImageMobile())
            ? static::getImagesPath() . "/$imageMobile"
            : null;
    }

    public function getTitle(): ?string
    {
        return $this->getLang()->getTitle();
    }

    public function getLang(bool|int $create = true): ImageSlideshowSlideLang
    {
        $langId = is_numeric($create) ? $create : 1;
        $lang = $this->lang[$langId];
        if (!$lang && $create) {
            $lang = new ImageSlideshowSlideLang();
            $lang->setParent($this)->setLang($langId);
            $this->lang[$langId] = $lang;
        }
        return $lang;
    }
}
