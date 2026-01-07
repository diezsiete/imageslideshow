<?php

namespace PrestaShop\Module\ImageSlideshow\Repository;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use PrestaShop\Module\ImageSlideshow\Entity\ImageSlideshow;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;

/**
 * @method ImageSlideshow|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageSlideshow|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageSlideshow[]    findAll()
 * @method ImageSlideshow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageSlideshowRepository extends ServiceEntityRepository implements FormDataProviderInterface
{
    public function __construct(Registry $managerRegistry)
    {
        parent::__construct($managerRegistry, ImageSlideshow::class);
    }

    public function exists(int|string $id): bool
    {
        return (bool) $this->getById($id, 'id');
    }

    public function getSlug(int|string $id): ?string
    {
        return $this->getById($id, 'slug');
    }

    public function getData($id)
    {
        return $this->getById($id, hydrationMode: AbstractQuery::HYDRATE_ARRAY);
    }

    public function getDefaultData(): array
    {
        return [];
    }


    public function findWithSlides(int|string $id): ?ImageSlideshow
    {
        return $this->createQueryBuilder('ims')
            ->select('ims, imss')
            ->leftJoin('ims.slides', 'imss')
            ->where('ims.id = :id')
            ->setParameter('id', $id)
            ->orderBy('imss.position')
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function getById(int|string $id, ?string $field = null, int $hydrationMode = AbstractQuery::HYDRATE_OBJECT): mixed
    {
        if (!$id) {
            return null;
        }
        $qb = $this->createQueryBuilder('ims')
            ->where("ims.id = :id")
            ->setParameter('id', $id);
        if ($field) {
            $qb->select("ims.$field");
            $hydrationMode = AbstractQuery::HYDRATE_SINGLE_SCALAR;
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        return $qb->getQuery()->getOneOrNullResult($hydrationMode);
    }
}
