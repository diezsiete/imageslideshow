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

    public function getData($id)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->createQueryBuilder('is')
            ->where("is.id = :id")->setParameter('id', $id)
            ->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getDefaultData(): array
    {
        return [];
    }
}
