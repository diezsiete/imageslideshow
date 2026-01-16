<?php

namespace PrestaShop\Module\ImageSlideshow\Repository;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
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
            'inset' => $slide->getInset(),
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

    public function exists(int|string $id, int|null|string $idSlideshow = null): bool
    {
        return (bool) $this->getById($id, $idSlideshow, 'id');
    }

    public function findSlide(int|string $id, int|null|string $idSlideshow = null): ?ImageSlideshowSlide
    {
        return $this->getById($id, $idSlideshow);
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

    private function getById(int|string $id, int|null|string $idSlideshow = null, ?string $field = null): mixed
    {
        $qb = $this->createQueryBuilder('ss')
            ->where('ss.id = :id')->setParameter('id', $id);
        if ($idSlideshow) {
            $qb->join('ss.slideshow', 's')->andWhere('s.id = :idSlideshow')->setParameter('idSlideshow', $idSlideshow);
        }
        if ($field) {
            $qb->select("ss.$field");
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        return $qb->getQuery()->getOneOrNullResult($field ? AbstractQuery::HYDRATE_SINGLE_SCALAR : null);
    }
}
