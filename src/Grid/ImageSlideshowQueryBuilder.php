<?php

namespace PrestaShop\Module\ImageSlideshow\Grid;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class ImageSlideshowQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * basedon \PrestaShop\PrestaShop\Core\Grid\Query\CmsPageQueryBuilder::__construct
     */
    public function __construct(
        Connection                                                 $connection,
        string                                                     $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder()->select('*');
        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getQueryBuilder()->select('COUNT(*)');
    }

    /**
     * basedon \PrestaShop\PrestaShop\Core\Grid\Query\CmsPageQueryBuilder::getQueryBuilder
     *         \PrestaShop\PrestaShop\Core\Grid\Query\OrderQueryBuilder::getBaseQueryBuilder
     */
    private function getQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()->from($this->dbPrefix . 'image_slideshow', 's');
    }
}
