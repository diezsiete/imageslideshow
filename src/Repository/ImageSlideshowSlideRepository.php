<?php

namespace PrestaShop\Module\ImageSlideshow\Repository;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PrestaShop\Module\ImageSlideshow\Entity\ImageSlideshow;
use PrestaShop\Module\ImageSlideshow\Entity\ImageSlideshowSlide;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

/**
 * @method ImageSlideshowSlide|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageSlideshowSlide|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageSlideshowSlide[]    findAll()
 * @method ImageSlideshowSlide[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageSlideshowSlideRepository extends ServiceEntityRepository implements FormDataProviderInterface
{
    public function __construct(Registry $managerRegistry)
    {
        parent::__construct($managerRegistry, ImageSlideshowSlide::class);
    }

    public function getData($id): array
    {
        $slide = $this->findSlide($id);
        return [
            'id' => $slide->getId(),
            'active' => $slide->isActive(),
            'targetBlank' => $slide->isTargetBlank(),
            'title' => $slide->getLang()->getTitle(),
            'description' => $slide->getLang()->getDescription(),
            'legend' => $slide->getLang()->getLegend(),
            'url' => $slide->getLang()->getUrl(),
            'image' => $slide->getLang()->getImage(),
            'imageMobile' => $slide->getLang()->getImageMobile(),
            'slideshow' => $slide->getSlideshow()
        ];
    }

    public function getDefaultData(): array
    {
        return [];
    }

    public function findSlide(int|string $id, int|null|string $idSlideshow = null): ?ImageSlideshowSlide
    {
        $qb = $this->createQueryBuilder('ss')
            ->where('ss.id = :id')->setParameter('id', $id);
        if ($idSlideshow) {
            $qb->join('ss.slideshow', 's')->andWhere('s.id = :idSlideshow')->setParameter('idSlideshow', $idSlideshow);
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string|null $orderByPosition ASC|DESC
     * @return ImageSlideshowSlide[]
     */
    public function findSlides(int|ImageSlideshow $slideshow, ?string $orderByPosition = 'ASC'): array
    {
        $qb = $this->createQueryBuilder('ss')
            ->andWhere('ss.slideshow = :slideshow')->setParameter('slideshow', $slideshow);
        if ($orderByPosition === "ASC" || $orderByPosition === "DESC") {
            $qb->orderBy('ss.position', $orderByPosition);
        }
        return $qb->getQuery()->getResult();
    }
}
